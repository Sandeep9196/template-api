<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentGatewayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $nameRule = $this->method() === 'POST' ?
            'required|unique:payment_gateways,name' : 'required|unique:payment_gateways,name,' . $this->payment->id;
        return [
            'name' => $nameRule
        ];
    }
}
