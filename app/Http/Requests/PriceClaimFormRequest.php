<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceClaimFormRequest extends FormRequest
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
        return [
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'required|exists:orders,id',
            'deal_id' => 'required|exists:deals,id',
            'booking_id' => 'required|exists:slot_deals,booking_id',
        ];
    }
}
