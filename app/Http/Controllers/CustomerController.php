<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerAccountFormRequest;
use App\Http\Requests\CustomerForgetPasswordFormRequest;
use App\Http\Requests\CustomerGetTransactionRequest;
use App\Http\Requests\CustomerLoginFormRequest;
use App\Http\Requests\CustomerPasswordFormRequest;
use App\Http\Requests\CustomerRegisterFormRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\CustomerSendOTPFormRequest;
use App\Http\Requests\CustomerVerifyOTFormRequest;
use App\Jobs\CreateCustomer;
use App\Services\CustomerService;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $customerService)
    {
    }

    public function sendOTP(CustomerSendOTPFormRequest $request)
    {
        return $this->customerService->sendOTP($request->all());
    }

    public function verifyOTP(CustomerVerifyOTFormRequest $request)
    {
        return $this->customerService->verifyOTP($request->all());
    }

    public function register(CustomerRegisterFormRequest $request)
    {
        return $this->customerService->register($request->all());
    }

    public function login(CustomerLoginFormRequest $request)
    {
        return $this->customerService->login($request->all());
    }

    public function forgetPassword(CustomerForgetPasswordFormRequest $request)
    {
        return $this->customerService->forgetPassword($request->all());
    }

    public function setNewPassword(CustomerPasswordFormRequest $request)
    {
        return $this->customerService->setNewPassword($request->all());
    }
    public function setCustomerNewPassword(CustomerPasswordFormRequest $request,Customer $customer)
    {
        return $this->customerService->setCustomerNewPassword($request->all(),$customer);
    }
    /**
     * @description update customer account detail controller function
     * @author Phen
     * @return JsonResponse
     * @date 06 Jan 2023
     */
    public function updateAccount(CustomerAccountFormRequest $request)
    {
        return $this->customerService->updateAccount($request);
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

    /**
     * @description get customer detail using token controller function
     * @author Sushil
     * @return JsonResponse
     * @date 06 Jan 2023
     */
    public function userDetails()
    {
        return $this->customerService->userDetails();
    }

    public function getCalculations(Request $request,Customer $customer)
    {
        return $this->customerService->getCalculations($request->all(), $customer);
    }

    public function getCalculationsCustomers(Request $request)
    {
        return $this->customerService->getCalculations($request->all(), Customer::whereId(auth()->user()->id)->first());
    }

    public function createBotCustomer(CustomerRequest $request)
    {
        CreateCustomer::dispatch($request->count);
        $message = ['messages' => $request->count.' Customers created successfully'];
        return response()->json($message,200);
    }

    public function getCounts(Request $request)
    {
        return $this->customerService->getCounts($request->all());
    }

    public function getTransactions(CustomerGetTransactionRequest $request)
    {
        return $this->customerService->getTransactions($request);
    }


}
