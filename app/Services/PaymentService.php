<?php

namespace App\Services;

use App\Http\Controllers\PaymentController;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Translation;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Payment())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->payment_id, function ($query) use ($request) {
                $query->where('payment_id', 'like', "%$request->payment_id%");
            });
            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', 'like', "%$request->status%");
            });
            $results = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data, $order)
    {
        try {
            $data['payment_id']  = rand();
            $data['customer_id'] = Auth()->user()->id;
            $data['order_id']    = $order['id'];
            $data['amount']      = $order['total_amount'];
            $data['provider']    = 'test';
            $data['status']      = 'complete';

            Payment::create($data);
            $order->update(['status' => 'confirmed']);
            $order->orderProduct()->update([
                'status' => 'confirmed'
            ]);

            return response()->json([
                'messages' => ['Payment created Successfully'],
            ], 201);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }
    public function getUnpaidPayments($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'order_product.created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new OrderProduct())->where('status', 'reserved')->newQuery()->orderBy($sortBy, $sortOrder);
            $results = $query->with(['product', 'customer'])->paginate($perPage, ['*'], 'page', $page);
            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function getUnpaidPaymentsApi($request): JsonResponse
    {
        try {
            Session::put("promotional_query_session", true);
            $ops =  OrderProduct::where('status', 'reserved')
                ->select('order_product.*')
                ->where('customer_id', Auth()->user()->id)
                ->with(['product.translation'])
                ->orderBy('order_product.id', 'desc')
                ->get();
            if (!$ops && empty($ops)) {
                return response()->json(['messages' => ['Data Not Found'],], 400);
            }

            foreach ($ops as $key => $opData) {
                $orderId =  Order::whereId($opData->order_id)->first()->order_id;
                $ops[$key]->orderId = $orderId;
            }
            return response()->json($ops, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function storePayment(array $data)
    {
        try {

            $orderProductsIds = $data['order_product_ids'];
            $orderProducts = OrderProduct::whereIn('id', $orderProductsIds)->where('customer_id', auth()->user()->id)->get();
            $orderIds = $orderProducts->pluck('order_id');
            $orderIds = array_unique($orderIds->toArray());
            $orderStatus = ['status' => 'confirmed'];
            $orderStatusParent = ['status' => 'confirmed'];
            $currencyId = 1;
            foreach ($orderProducts as $order) {

                $check = Payment::whereOrderId($order->order_id)->where('order_product_ids', $order->id)->first();
                $transaction = Transaction::create([
                    'transaction_ID' => getRandomIdGenerate('TR'),
                    'member_id' => auth()->user()->id,
                    'transaction_type' => TRANSFER_OUT,
                    'currency_id' => $currencyId,
                    'amount' => $order->amount,
                    'status' => "Pending",
                    'message' => "{{Payment transaction}}",
                ]);
                if (!$check) {
                    $dataPayment['payment_id']  = rand();
                    $dataPayment['customer_id'] = Auth()->user()->id;
                    $dataPayment['order_id']    = $order->id;
                    $dataPayment['amount']      = $order->amount;
                    $dataPayment['transaction_id']      = !empty($transaction->id) ? $transaction->id : 1;
                    $dataPayment['provider']    = 'test';
                    $dataPayment['status']      = 'complete';

                    Payment::create($dataPayment);
                }
                OrderProduct::whereId($order->id)->update($orderStatus);

                $orderProductCount = OrderProduct::where('order_id', $order->order_id)->count();
                $orderProductCountConfirm = OrderProduct::where('order_id', $order->order_id)->where('status', 'confirmed')->count();
                if ($orderProductCountConfirm == $orderProductCount) {
                    $orderStatusParent = ['status' => 'confirmed'];
                } else {
                    $orderStatusParent = ['status' => 'remaining'];
                }
                Order::where('id', $order->order_id)->update($orderStatusParent);
            }

            return response()->json([
                'messages' => ['Payment created Successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function getPaymentMethods()
    {
        try {
            $params = [
                "service" => "webpay.acquire.getpaymentmethods",
                "sign_type" => "MD5",
            ];
            $params['sign'] = signature($params, getConfigs()['api_secret_key']);
            $url = getConfigs()['url'] . '/api/mch/v2/gateway';
            $resp = callHttp($url, $params);
            return response()->json([
                'data' => $resp,
                'message' => 'Get payment method successfully!'
            ], 200);
        } catch (\Throwable $th) {
            return generalErrorResponse($th);
        }
    }
}
