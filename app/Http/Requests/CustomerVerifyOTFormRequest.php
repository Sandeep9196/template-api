<?php

namespace App\Http\Requests;

use App\Traits\PhoneNumberSerializable;
use Illuminate\Foundation\Http\FormRequest;

class CustomerVerifyOTFormRequest extends FormRequest
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
            'phone_number' => 'required|numeric',
            'idd' => 'required|numeric',
            'otp' => 'required|numeric',
            'type' => 'in:register,forget_password',
        ];
    }

}
