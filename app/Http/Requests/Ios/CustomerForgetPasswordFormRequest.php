<?php

namespace App\Http\Requests\Ios;

use App\Traits\PhoneNumberSerializable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\FailedMobValidation;

class CustomerForgetPasswordFormRequest extends FormRequest
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
            'phone_number' => ['required',
                Rule::exists('customers')->where(function ($query) {
                    return $query->where('phone_number', $this->phone_number)
                                ->where('idd', $this->idd)
                                ->where('deleted_at', NULL);
                }),
            ],
            'idd' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'phone_number.exists' => 'Phone Number Not Registered',
        ];
    }

}
