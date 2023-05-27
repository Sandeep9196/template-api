<?php

namespace App\Http\Controllers\Ios;

use App\Http\Requests\CustomerAccountFormRequest;
use App\Http\Requests\Ios\CustomerForgetPasswordFormRequest;
use App\Http\Requests\Ios\CustomerLoginFormRequest;
use App\Http\Requests\Ios\CustomerPasswordFormRequest;
use App\Http\Requests\Ios\CustomerRegisterFormRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\Ios\CustomerSendOTPFormRequest;
use App\Http\Requests\Ios\CustomerVerifyOTFormRequest;
use App\Jobs\CreateCustomer;
use App\Services\Ios\LoginService;
use App\Models\Customer;
use Illuminate\Http\Request;

class LoginController extends \App\Http\Controllers\Controller
{
    public function __construct(private LoginService $loginService)
    {
    }

    public function sendOTP(CustomerSendOTPFormRequest $request)
    {
        return $this->loginService->sendOTP($request->all());
    }

    public function verifyOTP(CustomerVerifyOTFormRequest $request)
    {
        return $this->loginService->verifyOTP($request->all());
    }

    public function register(CustomerRegisterFormRequest $request)
    {
        return $this->loginService->register($request->all());
    }

    public function login(CustomerLoginFormRequest $request)
    {
        return $this->loginService->login($request->all());
    }

    public function forgetPassword(CustomerForgetPasswordFormRequest $request)
    {
        return $this->loginService->forgetPassword($request->all());
    }

    public function setNewPassword(CustomerPasswordFormRequest $request)
    {
        return $this->loginService->setNewPassword($request->all());
    }

    /**
     * @description get customer detail using token controller function
     * @author Sushil
     * @return JsonResponse
     * @date 06 Jan 2023
     */
    public function userDetails()
    {
        return $this->loginService->userDetails();
    }





    /**
     * @description update customer account detail controller function
     * @author Phen
     * @return JsonResponse
     * @date 06 Jan 2023
     */
    public function updateAccount(CustomerAccountFormRequest $request)
    {
        return $this->loginService->updateAccount($request);
    }

    /**
     * @description get customer account detail controller function
     * @author Phen
     * @return JsonResponse
     * @date 06 Jan 2023
     */
    public function get(Customer $customer)
    {
        return response()->json($customer, 200);
    }

    public function getCalculations(Request $request, Customer $customer)
    {
        return $this->loginService->getCalculations($request->all(), $customer);
    }

    public function getCalculationsCustomers(Request $request)
    {
        return $this->loginService->getCalculations($request->all(), Customer::whereId(auth()->user()->id)->first());
    }

    public function createBotCustomer(CustomerRequest $request)
    {
        CreateCustomer::dispatch($request->count);
        $message = ['messages' => $request->count . ' Customers created successfully'];
        return response()->json($message, 200);
    }
}
