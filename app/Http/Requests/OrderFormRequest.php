<?php

namespace App\Http\Requests;

use App\Rules\ExternalSystemRoleRule;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderFormRequest extends FormRequest
{
    use FailedValidation;

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
        $rules = [];

        if ($this->is('api/customer/orders') == true || $this->is('api/admin/orders') == true) {
            $rules =  [
                'product_details' => 'required|array|min:1',
                'product_details.*.product_id' => $this->method() === 'POST' ? 'required|exists:products,id,status,active' : 'exists:products,id,status,active',
                'product_details.*.amount' => $this->method() === 'POST' ? 'required|numeric' : ''
            ];
        }

        if ($this->is('api/customer/orders-cancel') == true) {
            $rules = [
                'order_product_id' =>
                [
                    'required',
                    'array',
                    'min:1',
                    'exists:order_product,id,customer_id,'.auth()->user()->id,
                ]
            ];
        }
        return $rules;
    }

    public function messages()
    {
        $messages = [];
        if ($this->is('api/customer/orders') == true || $this->is('api/admin/orders') == true) {
            $messages =  [
                "product_details.required" => "Cart is Empty",
                'product_details.array' => 'The product details must be an array.',
                'product_details.min' => 'The product details must have at least one item.',
                'product_details.*.product_id.required' => 'The product ID field is required.',
                'product_details.*.product_id.exists' => 'Selected product is invalid',
                'product_details.*.amount.required' => 'The amount field is required.'
            ];
        }
        return $messages;
    }
}
