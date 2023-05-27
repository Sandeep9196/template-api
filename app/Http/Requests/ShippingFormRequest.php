<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShippingFormRequest extends FormRequest
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
            'order_id' => [
                'required',
                Rule::exists('price_claims')->where(function ($query) {
                    $query->where('deleted_at', null);
                }),
            ],
            'booking_id' => [
                'required',
                Rule::exists('price_claims')->where(function ($query) {
                    $query->where('deleted_at', null);
                }),
            ],
            'customer_id' => [
                'required',
                Rule::exists('price_claims')->where(function ($query) {
                    $query->where('deleted_at', null);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'order_id.exists' => 'Invalid Order id',
            'booking_id.exists' => 'Invalid Booking id',
            'customer_id.exists' => 'Invalid Customer id',
        ];
    }
}
