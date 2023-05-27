<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentFormRequest;
use App\Http\Requests\PaymentResponseFormRequest;
use App\Models\Order;
use App\Models\payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;



class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->paymentService->paginate($request);
    }

    public function all()
    {
        return response()->json(Payment::all(), 200);
    }

     public function store(PaymentFormRequest $request, Order $order)
    {
        return $this->paymentService->store($request->all(), $order);
    }
     public function storePayment(PaymentFormRequest $request)
    {
        return $this->paymentService->storePayment($request->all());
    }
    public function getUnpaidPayments(Request $request)
    {
        return $this->paymentService->getUnpaidPayments($request);
    }
    public function getPaymentMethods(PaymentResponseFormRequest $request)
    {
        return $this->paymentService->getPaymentMethods($request);
    }
    public function getUnpaidPaymentsApi(Request $request)
    {
        return $this->paymentService->getUnpaidPaymentsApi($request);
    }



}
