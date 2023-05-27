<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentGatewayRequest;
use App\Models\PaymentGateway;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function __construct(private PaymentGatewayService $paymentGatewayService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->paymentGatewayService->paginate($request);
    }

    public function all()
    {
        return response()->json(PaymentGateway::all(), 200);
    }

    public function store(PaymentGatewayRequest $request)
    {
        return $this->paymentGatewayService->store($request->all());
    }

    public function update(PaymentGatewayRequest $request, PaymentGateway $payment)
    {
        return $this->paymentGatewayService->update($request->all(), $payment);
    }
    public function delete(PaymentGateway $payment)
    {
        return $this->paymentGatewayService->delete($payment);
    }

}
