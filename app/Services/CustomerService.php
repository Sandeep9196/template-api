<?php

namespace App\Services;

use App\Http\Requests\CustomerGetTransactionRequest;
use App\Models\Address;
use App\Models\Configure;
use App\Models\Customer;
use App\Models\Favorite;
use App\Models\LoginLog;
use App\Models\OntimePassword;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use stdClass;

class CustomerService
{

    public function sendOTP($data): JsonResponse
    {
        try {
            //save customer data in session
            if (isset($data['lang_id'])) {
                $langId = $data['lang_id'];
            } else {
                $langId = 1;
            }
            $otpData = sendOTP($data['idd'], $data['phone_number'], $langId);

            if ($otpData) {
                $result['message'] = 'otp_send_successfully';
                $result['statusCode'] = 200;

                return getSuccessMessages($result);
            }

            $result['message'] = 'otp_not_send';
            $result['statusCode'] = 201;

            return getSuccessMessages($result, false);
        } catch (\Exception $e) {
            // \Log::debug($e);
            return generalErrorResponse($e);
        }
    }
    public function verifyOTP($data): JsonResponse
    {
        try {
            $type = $data['type'] ?? 'register';
            $verifyResult = verifyOTP($data['idd'], $data['phone_number'], $data['otp'], $type);
            if ($verifyResult['status']) {
                return response()->json([
                    'status' => true,
                    'messages' => ['OTP verified successfully'],
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'messages' => [$verifyResult['msg']],
                ], 400);
            }
        } catch (\Exception $e) {
            // \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function register($data, $type = null): JsonResponse
    {
        try {

            $otp = OntimePassword::whereIdd($data['idd'])->wherePhoneNumber($data['phone_number'])->whereType('register')->first();
            if ($otp) {
                if ($otp->is_verify) {
                    $customer = Customer::whereIdd($data['idd'])->wherePhoneNumber($data['phone_number'])->first();

                    if ($customer) {
                        if ($type == 'mob') {
                            $result['message'] = 'customer_already_registered';
                            $result['statusCode'] = 400;

                            return getSuccessMessages($result, false);
                        }
                        return response()->json([
                            'status' => false,
                            'messages' => ['customer already registered'],
                        ], 200);
                    }
                    $nexCustomerId = DB::table('customers')->max('id') + 1;
                    $append = zeroappend($nexCustomerId);
                    $memberID = 'M' . $append . $nexCustomerId;
                    $data['member_ID'] = $memberID . rand(00, 99);

                    //create customer
                    $customer = Customer::create($data);
                    //assing role
                    $customer->assignRole('Customer');

                    if ($type == 'mob') {
                        $result['message'] = 'customer_already_registered';
                        $result['statusCode'] = 200;

                        return getSuccessMessages($result);
                    }
                    return response()->json([
                        'status' => true,
                        'messages' => ['customer registered successfully'],
                    ], 200);
                } else {
                    if ($type == 'mob') {
                        $result['message'] = 'OTP_not_yet_verified';
                        $result['statusCode'] = 400;

                        return getSuccessMessages($result, false);
                    }
                    return response()->json([
                        'status' => false,
                        'messages' => ['OTP not yet verified'],
                    ], 400);
                }
            }
            /* This is a response to the client. */

            if ($type == 'mob') {
                $result['message'] = 'customer_registration_failed';
                $result['statusCode'] = 400;

                return getSuccessMessages($result, false);
            }

            return response()->json([
                'status' => false,
                'messages' => ['customer registration failed'],
            ], 200);
        } catch (\Exception $e) {
            // \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function login(array $data, $type = null): JsonResponse
    {
        $loginData = [
            "phone_number" => $data['phone_number'],
            "idd" => $data['idd'],
            "password" => $data['password']
        ];
        try {
            if (Auth::guard('customer')->attempt($loginData)) {
                $customer = Auth::guard('customer')->user();
                $customer->tokens()->delete();


                LoginLog::create([
                    'user_id' => $customer->id,
                    'user_type' => Customer::class,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);


                if ($type == 'mob') {
                    $result['message'] = 'login_successfully';
                    $result['statusCode'] = 200;
                    $result['data'] = [
                        'customer' => $customer,
                        'notifications' => $customer->notifications(),
                        'wallet' => [],
                        'token' => $customer->createToken($customer->phone_number)->plainTextToken,
                    ];

                    return getSuccessMessages($result);
                }

                return response()->json([
                    'status' => true,
                    'customer' => $customer,
                    'notifications' => $customer->notifications(),
                    'token' => $customer->createToken($customer->phone_number)->plainTextToken,
                ], 200);
            }
            //check if user not yet registered
            unset($loginData['password']);
            if (!Customer::where($loginData)->first()) {

                if ($type == 'mob') {
                    $result['message'] = 'This_user_not_yet_registered';
                    $result['statusCode'] = 400;

                    return getSuccessMessages($result, false);
                }

                return response()->json([
                    'status' => false,
                    'messages' => ['This user not yet registered'],
                ], 401);
            }

            if ($type == 'mob') {
                $result['message'] = 'Incorrect_Username_or_password';
                $result['statusCode'] = 400;

                return getSuccessMessages($result, false);
            }
            return response()->json([
                'status' => false,
                'messages' => ['Incorrect Username or password'],
            ], 401);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function forgetPassword(array $data): JsonResponse
    {
        try {
            if (isset($data['lang_id'])) {
                $langId = $data['lang_id'];
            } else {
                $langId = 1;
            }
            $otpData = sendOTP($data['idd'], $data['phone_number'], $langId, 'forget_password');

            if ($otpData) {
                $result['message'] = 'otp_send_successfully';
                $result['statusCode'] = 200;

                return getSuccessMessages($result);
            }

            $result['message'] = 'otp_not_send';
            $result['statusCode'] = 201;

            return getSuccessMessages($result, false);
        } catch (\Exception $e) {
            // \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function setNewPassword(array $data, $type = null): JsonResponse
    {
        try {
            $otp = OntimePassword::whereIdd($data['idd'])->wherePhoneNumber($data['phone_number'])->whereType('forget_password')->first();
            if ($otp) {
                if ($otp->is_verify) {
                    $customer = Customer::whereIdd($data['idd'])->wherePhoneNumber($data['phone_number'])->first();

                    //check if password the same as old password
                    if (Hash::check($data['password'], $customer->password)) {
                        if ($type == 'mob') {
                            $result['message'] = 'This_is_your_old_password';
                            $result['statusCode'] = 400;

                            return getSuccessMessages($result, false);
                        }
                        return response()->json([
                            'status' => false,
                            'messages' => ['This is your old password. Use the new one.'],
                        ], 400);
                    }
                    $customer->update(['password' => $data['password']]);
                    if ($type == 'mob') {
                        $result['message'] = 'password_reset_successfully';
                        $result['statusCode'] = 200;

                        return getSuccessMessages($result);
                    }
                    return response()->json([
                        'status' => true,
                        'messages' => ['password reset successfully'],
                    ], 200);
                } else {
                    if ($type == 'mob') {
                        $result['message'] = 'OTP_not_yet_verified';
                        $result['statusCode'] = 400;

                        return getSuccessMessages($result, false);
                    }
                    return response()->json([
                        'status' => false,
                        'messages' => ['OTP not yet verified'],
                    ], 400);
                }
            }
            if ($type == 'mob') {
                $result['message'] = 'reset_password_failed';
                $result['statusCode'] = 400;

                return getSuccessMessages($result, false);
            }
            return response()->json([
                'status' => false,
                'messages' => ['reset password failed'],
            ], 200);
        } catch (\Exception $e) {
            // \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function setCustomerNewPassword(array $data, $customer): JsonResponse
    {
        try {
            $customer->update(['password' => $data['password']]);
            return response()->json([
                'status' => true,
                'messages' => ['password reset successfully'],
            ], 200);
        } catch (\Exception $e) {
            // \Log::debug($e);
            return generalErrorResponse($e);
        }
    }



    public function logout()
    {
        if (Auth::guard('customer')->user()) {
            Auth::guard('customer')->user()->tokens()->delete();
            Auth::guard('web')->logout();
        }

        return response('Logout successful', 204);
    }

    /**
     * @description update customer account detail service function
     * @author Phen
     * @return JsonResponse
     * @date 06 Jan 2023
     */
    public function updateAccount($request): JsonResponse
    {
        try {
            $customerData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'display_name' => $request->display_name,
            ];
            if ($request->current_password) {
                if (Hash::check($request->current_password, Auth::user()->password)) {
                    $customerData['password'] = $request->new_password;
                } else {
                    $result['status'] = false;
                    $result['message'] = 'current_password_not_correct';
                    return getSuccessMessages($result, false);
                }
            }
            Auth::user()->update($customerData);

            return response()->json([
                'status' => true,
                'messages' => ['Account has been saved successfully'],

            ], 200);
        } catch (\Exception $e) {
            // \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function userDetails(): JsonResponse
    {
        try {
            $result['orderCount']   = Order::where('customer_id', Auth()->user()->id)->count();
            $result['whishlistDetails'] = $this->getFavouriteData();
            $result['whishlistCount'] = Favorite::where('customer_id', Auth()->user()->id)->whereHas('product', function ($query) {
            })->count();
            $result['customer'] = Customer::whereId(Auth()->id())->first();
            return response()->json([
                'status' => true,
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function getFavouriteData()
    {
         $favourite = array();
         $favourites = Favorite::where('customer_id', Auth()->user()->id)->whereHas('product', function ($query) {
        })->get();

        $response = [];
        if(!empty($favourites)){
            foreach($favourites as $key => $fav){
                $response['id'] = $fav['id'];
                $response['product_id'] = $fav['product_id'];
                $response['slug'] = Product::whereId($fav['product_id'])->first()->slug;
                $response['product_image'] = Product::with(['productImage:id,path,fileable_id'])->first()->productImage;
                $response['customer_id'] = $fav['customer_id'];
                $response['created_at'] = $fav['created_at'];
                $response['updated_at'] = $fav['updated_at'];
                $response['deleted_at'] = $fav['deleted_at'];
                $favourite[] = $response;
            }
        }
        return $favourite;
    }

    public function getCalculations($request, $customer): JsonResponse
    {
        try {
            $mlmData = Configure::where('type', 'MLM')->select('data')->first();
            if (empty($mlmData->data)) {
                return response()->json([
                    'message' => 'Please set the MLM Configuration ',
                ], 404);
            }
            $mlmConfiguration = json_decode($mlmData->data);
            $L1Customers = Customer::withSum('orderProduct', 'amount')->with('parent')->where('parent_referral_code', $customer->referral_code)->get();
            $L1CustomersId = collect($L1Customers->pluck('referral_code'));
            $L1CustomersIds = collect($L1Customers->pluck('id'));

            $L2Customers = Customer::withSum('orderProduct', 'amount')->with('parent.parent')->whereIn('parent_referral_code', $L1CustomersId)->get();
            $L2CustomersId = collect($L2Customers->pluck('referral_code'));
            $L2CustomersIds = collect($L2Customers->pluck('id'));

            $L3Customers = Customer::withSum('orderProduct', 'amount')->with('parent.parent.parent')->whereIn('parent_referral_code', $L2CustomersId)->get();
            $L3CustomersIds = collect($L3Customers->pluck('id'));

            $L1OrdersValue = OrderProduct::whereIn('customer_id', $L1CustomersIds)->where('status', '!=', 'reserved')->sum('amount');
            $L2OrdersValue = OrderProduct::whereIn('customer_id', $L2CustomersIds)->where('status', '!=', 'reserved')->sum('amount');
            $L3OrdersValue = OrderProduct::whereIn('customer_id', $L3CustomersIds)->where('status', '!=', 'reserved')->sum('amount');

            $result = [];
            $obj1 = new stdClass();
            $obj2 = new stdClass();
            $obj3 = new stdClass();

            $obj1->members = $L1Customers;
            $obj1->members_count = count($L1Customers);
            $obj1->transaction_amount = $L1OrdersValue;
            if ($L1OrdersValue > 0 && $mlmConfiguration->level_one_status == 'active')
                $obj1->commission = ($mlmConfiguration->level_one_commission * $L1OrdersValue) /  100;
            else
                $obj1->commission = 0;

            $obj2->members = $L2Customers;
            $obj2->members_count = count($L2Customers);
            $obj2->transaction_amount = $L2OrdersValue;
            if ($L2OrdersValue > 0 && $mlmConfiguration->level_two_status == 'active')
                $obj2->commission = ($mlmConfiguration->level_two_commission  *  $L2OrdersValue) / 100;
            else
                $obj2->commission = 0;


            $obj3->members = $L3Customers;
            $obj3->members_count = count($L3Customers);
            $obj3->transaction_amount = $L3OrdersValue;
            if ($L3OrdersValue > 0 && $mlmConfiguration->level_three_status == 'active')
                $obj3->commission = ($mlmConfiguration->level_three_commission * $L3OrdersValue) /  100;
            else
                $obj3->commission = 0;


            $result['member'] = $customer;
            $result['level_one'] = $obj1;
            $result['level_two'] = $obj2;
            $result['level_three'] = $obj3;

            $result['total_members'] = count($L1Customers) + count($L2Customers) + count($L3Customers);
            $result['total_members_data'] = getArrayCollections([$L1Customers, $L2Customers, $L3Customers]);
            $result['total_transaction'] = $obj1->transaction_amount + $obj2->transaction_amount + $obj3->transaction_amount;
            $result['total_commissions'] = $obj1->commission + $obj2->commission + $obj3->commission;


            return response()->json($result, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function getCounts(): JsonResponse
    {
        try {

            $result = Customer::where('id', Auth()->user()->id)->count();
            return response()->json([
                'total_customers' => $result,
            ], 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }


    /**
     * @desc store getting customer transaction list
     * @param $request
     * @return JsonResponse
     * @date 11 Jan 2023
     * @author Phen
     */
    public function getTransactions($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';
            $query = (new Transaction())->newQuery()
                ->whereMemberId(auth::id())
                ->orderBy($sortBy, $sortOrder);

            $query->when($request->transaction_type, function ($query) use ($request) {
                $query->where('transaction_type', $request->transaction_type);
            });
            $query->when($request->date_range, function ($query) use ($request) {
                $dates = explode(' - ', $request->date_range);
                $dates[0] = Carbon::parse($dates[0])->startOfDay()->format('Y-m-d H:i:s');
                $dates[1] = Carbon::parse($dates[1])->endOfDay()->format('Y-m-d H:i:s');
                $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            });
            $query->when($request->currency_id, function ($query) use ($request) {
                $query->where('currency_id', $request->currency_id);
            });
            $query->when($request->transaction_ID, function ($query) use ($request) {
                $query->where('transaction_ID', 'like', "%$request->transaction_ID%");
            });
            $query->when($request->amount, function ($query) use ($request) {
                $query->where('amount', 'like', "%$request->amount%");
            });
            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });
            $results = $query->select('transactions.*')->with('image')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }
}
