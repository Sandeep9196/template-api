<?php

namespace App\Http\Requests;

use App\Traits\PhoneNumberSerializable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerSendOTPFormRequest extends FormRequest
{
    use PhoneNumberSerializable;

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
            'phone_number' => [
                'required',
                'numeric',
                Rule::unique('customers')->where(function ($query) {
                    $query->where('idd', $this->idd)->where('deleted_at',null);
                })
            ],
            'idd' => 'required|numeric',
        ];
    }
    public function messages()
    {
        return [
            'phone_number.unique' => 'Phone Number already registered',
        ];
    }

}
