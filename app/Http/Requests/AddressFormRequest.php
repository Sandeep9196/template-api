<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressFormRequest extends FormRequest
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

            'street_address_1' => $this->method() === 'POST' ? 'required': "",
            'street_address_2' => $this->method() === 'POST' ? 'nullable': "",
            'pincode' => $this->method() === 'POST' ? 'required': "",
            'type' => $this->method() === 'POST' ? 'required|in:billing,shipping': "",

        ];
    }
}
