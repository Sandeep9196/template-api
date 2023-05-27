<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\WithdrawDetail as WithDrawDetail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'transactions.created_at';
            $sortOrder = $request->descending == 'false' ? 'desc' : 'asc';

            $query = (new Transaction())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->dates, function ($query) use ($request) {
                if ($request->dates[0] == $request->dates[1]) {
                    $query->whereDate('transactions.created_at', Carbon::parse($request->dates[0])->format('Y-m-d'));
                } else {
                    $query->whereBetween('transactions.created_at', [
                        Carbon::parse($request->dates[0])->startOfDay(),
                        Carbon::parse($request->dates[1])->endOfDay(),
                    ]);
                }
            });

            $query->when($request->transaction_type, function ($query) use ($request) {
                $query->where('transaction_type', $request->transaction_type);
            });
            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });
            $query->when($request->currency_id, function ($query) use ($request) {
                $query->where('currency_id', $request->currency_id);
            });
            $query->when($request->transaction_ID, function ($query) use ($request) {
                $query->where('transaction_ID', 'like', "%$request->transaction_ID%");
            });
            $query->when($request->member_id, function ($query) use ($request) {
                $query->leftJoin('customers', 'customers.id', 'transactions.member_id')
                    ->where('customers.member_ID', 'like', "%$request->member_id%");
            });
            $query->when($request->amount, function ($query) use ($request) {
                $query->where('amount', 'like', "%$request->amount%");
            });

            $results = $query->select('transactions.*')->with(['currency:id,code,symbol', 'customer:id,member_ID','image:path,fileable_id',])->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            $paymentFail = false;
            $currencyCode = "USD";
            $responseData = array();
            $redirectUrl = @$data['redirect_url']??"http://the1shops.com/deposit";
            $currency = Currency::find(@$data['currency_id'])->first();
            if ($currency)
                $currencyCode = $currency->code;

            $data['status'] = 'Pending';
            $data['transaction_ID'] = getRandomIdGenerate('TR');

            if (!@$data['member_id']) {
                $customer = Customer::find(auth()->id());
                if (!$customer){
                    if(isset($data['device_type']) && $data['device_type'] == 'mob'){
                        $result['message'] = 'This_user_cannot_create_transaction';
                        $result['statusCode'] = 201;
                        return getSuccessMessages($result, false);
                    }
                    return response()->json([
                        'messages' => ['This user cannot create transaction'],
                    ], 400);
                }

                if(isset($data['device_type']) && $data['device_type'] == 'mob'){
                    $result['message'] = 'Transaction_created_successfully';
                    $result['data'] = ['transaction_ID' => $data['transaction_ID']];
                    $result['statusCode'] = 200;
                    return getSuccessMessages($result);
                }
                $data['member_id'] = $customer->id;
            }

            $transaction = Transaction::create($data);

            saveFiles($transaction, 'image', @$data['file']);

            //send request to third party server
            //pay with third party api
            $params = [
                "service" => "webpay.acquire.createOrder",
                "sign_type" => "MD5",
                "seller_code" => getConfigs()['seller_code'],
                "out_trade_no" => $data['transaction_ID'],
                "body" => "Wallet Top up",
                "total_amount" => $data['amount'],
                "currency" => $currencyCode,
                "notify_url" => url('/').'/api/transactions/deposit-response',
                "expires_in" => 3600,
                "redirect_url"=> $redirectUrl
            ];
            $params['sign'] = signature($params,getConfigs()['api_secret_key']);

            $url = getConfigs()['url'].'/api/mch/v2/gateway';

            try {
                $resp = callHttp($url,$params);
                // update transaction data
                Transaction::where('id',$transaction->id)->update([
                    'request_data' => $params,
                    'response_data' => $resp
                ]);
                if($resp['success'] == true) {
                    $responseData['payment_link'] = @$resp['data']['payment_link'];

                }
                else{
                    $paymentFail = true;
                    \Log::error($resp);
                }

            } catch (\Throwable $th) {
                $paymentFail = true;
                \Log::error($th);
            }
            $responseData['amount'] = $data['amount'];
            $responseData['external_order_id'] = $data['transaction_ID'];
            $responseData['payment_type'] = 'p';

            $response = [
                'transaction_ID' => $data['transaction_ID'],
                'messages' => ['Transaction created successfully'],
                'data' => [],
            ];
            if(!$paymentFail){
                $response['data'] = $responseData;
            }

            if(isset($data['device_type']) && $data['device_type'] == 'mob'){

                $result['message'] = 'Transaction_created_successfully';
                $result['data'] = $responseData;
                $result['statusCode'] = 200;
                return getSuccessMessages($result);
            }

            return response()->json($response, 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function update($transaction, array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($transaction, $data) {
                $transaction->update($data);

                saveFiles($transaction, 'image', @$data['file']);

                if($data['status'] == 'Success' && TRANSFER_IN == $transaction->transaction_type){
                    updateCustomerWallet($transaction->member_id, $transaction->currency_id, TRANSFER_IN, $transaction->amount);
                }

                if($data['status'] == 'Success' && WITHDRAW == $transaction->transaction_type){
                    \Log::debug("message");
                    updateCustomerWallet($transaction->member_id, $transaction->currency_id, WITHDRAW, $transaction->amount);
                }

                WithDrawDetail::where(['transaction_id' => $transaction->id])->delete();
                $withDrawData = [
                    'transaction_id' =>  $transaction->id,
                     'status' => !empty($data['status']) ? $data['status'] : '',
                      'transaction_id' => $transaction->id,
                      'user_id' => $transaction->member_id,
                //    'created_by' => Auth::user()->id,
                    'comment' => !empty($data['comment']) ? $data['comment'] : ''
                ];
                WithDrawDetail::create($withDrawData);

            });

            return response()->json([
                'messages' => ['Transaction updated successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($transaction): JsonResponse
    {
        try {
            DB::transaction(function () use ($transaction) {
                $transaction->delete();
                WithDrawDetail::where(['transaction_id' => $transaction->id])->delete();
            });
            return response()->json([
                'messages' => ['Transaction deleted successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function updateStatus(array $data): JsonResponse
    {
        try {
            $status = '';

            $transaction = Transaction::where(['transaction_ID' => $data['transaction_ID']])->first();
            if (in_array($transaction->status, ['Credit', 'Reject'])) {
                return response()->json([
                    'messages' => ['This transaction has been updated!'],
                ], 400);
            }
            if ($data['status'] == "Success") {
                $transaction->update(['status' => 'Success']);
                $status = 'Credit';
                updateCustomerWallet(Auth::id(), $transaction->currency_id, TRANSFER_IN, $transaction->amount);
            }
            if ($data['status'] == "Fail") {
                $transaction->update(['status' => 'Fail']);
                $status = 'Reject';
            }

            //WithDrawDetail::where(['transaction_id' => $transaction->id])->delete();
            $withDrawData = [
                'transaction_id' =>  $transaction->id,
                 'status' => !empty($status) ? $status : '',
                  'transaction_id' => $transaction->id,
                  'user_id' => $transaction->member_id,
                  //'created_by' => Auth::user()->id,
                'comment' => !empty($data['comment']) ? $data['comment'] : ''
            ];
            WithDrawDetail::create($withDrawData);


            return response()->json([
                'transaction_ID' => $data['transaction_ID'],
                'messages' => ['Transaction updated status successfully'],
            ], 201);
        } catch (\Exception $e) {
            \Log::error($e);
            return generalErrorResponse($e);
        }
    }


    public function tranferAmount($request): JsonResponse {
        try {
            $withdraw = Transaction::whereTransactionType('withdraw')->count();
            $transfer_in = Transaction::whereTransactionType('transfer_in')->count();
            $transfer_out = Transaction::whereTransactionType('transfer_out')->count();
            return response()->json(['withdraw' => $withdraw, 'transfer_in' => $transfer_in, 'transfer_out' => $transfer_out], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    // public function withdraw($request): JsonResponse
    // {
    //     try {
    //         $currencyId = 1;
    //         $currency = Currency::find($request->currency_id);
    //         if($currency)
    //             $currencyId = $currency->id;

    //         //check customer balance availability
    //         if(withDrawAmount()+$request['amount'] > customerRemainAmount())
    //         {
    //             return response()->json([
    //                 'messages' => ['You cannot rquest withdraw, you don\'t have enough balance'],
    //             ], 400);
    //         }
    //         $data['status'] = 'Review';
    //         $data['transaction_ID'] = getRandomIdGenerate('TR');
    //         $data['member_id'] = auth()->id();
    //         $data['amount'] = $request->amount;
    //         $data['bank_account_id'] = $request->bank_account_id;
    //         $data['transaction_type'] = 'withdraw';
    //         $data['currency_id'] = $currencyId;
    //         $data['message'] = "{{Customer withdrawed Amount}}: " . $data['amount'];

    //         $transaction = Transaction::create($data);
    //         saveFiles($transaction, 'image', @$data['file']);

    //         return response()->json([
    //             'transaction_ID' => $data['transaction_ID'],
    //             'messages' => ['withdrawal_request_submitted'],
    //         ], 201);
    //     } catch (\Exception $e) {
    //         \Log::error($e);
    //         return generalErrorResponse($e);
    //     }
    // }

    /**
     * @desc deposit response from third party payment gateway
     * @param $resource
     * @param $relations_to_cascade
     * @return integer
     * @date 27 Feb 2023
     * @author Phen
     */
    public function depositResponse($request): JsonResponse
    {
        try {
            DB::beginTransaction();
                $transactionStatus = 'Fail';
                \Log::debug('This is response from third party in deposit response');
                \Log::debug($request);
                \Log::debug(gettype($request));
                $status = @$request['status'];
                $externalOrderID = @$request['out_trade_no'];

                $transaction = Transaction::whereTransactionId($externalOrderID)->first();

                if(!$transaction){
                    return response()->json([
                        'messages' => ['Update Transaction not found'],
                    ], 200);
                }
                if(in_array($transaction->status ,['Approve','Success']) )
                    return response()->json([
                        'messages' => ['Transaction Already updated'],
                    ], 400);

                if($status == 'SUCCESS'){
                    $transactionStatus = 'Success';
                    updateCustomerWallet($transaction->member_id, $transaction->currency_id, TRANSFER_IN, $transaction->amount);
                }
                //update transaction
                $transaction->update([
                    'final_response' => $request->all(),
                    'status' => $transactionStatus
                ]);

            DB::commit();

            return response()->json([
                'messages' => ['Deposit resonsed successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

}
