<?php

namespace App\Http\Requests\Ios;

use App\Traits\PhoneNumberSerializable;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\FailedMobValidation;

class CustomerRegisterFormRequest extends FormRequest
{
    use PhoneNumberSerializable, FailedMobValidation;

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
            'display_name' => 'nullable',
            'phone_number' => 'required',
            'idd' => 'required',
            'password' => 'required',
            'referral_code' => 'nullable',
        ];
    }
}
