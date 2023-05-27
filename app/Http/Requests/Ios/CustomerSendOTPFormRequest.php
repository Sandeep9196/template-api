<?php

namespace App\Http\Requests\Ios;

use App\Traits\PhoneNumberSerializable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\FailedMobValidation;


class CustomerSendOTPFormRequest extends FormRequest
{
    use PhoneNumberSerializable;
    use FailedMobValidation;
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
                Rule::unique('customers')->where(function ($query) {
                    $query->where('idd', $this->idd)->where('deleted_at',null);
                })
            ],
            'idd' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'phone_number.unique' => 'Phone Number already registered',
        ];
    }

}
