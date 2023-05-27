<?php

namespace App\Http\Requests;

use App\Traits\PhoneNumberSerializable;
use Illuminate\Foundation\Http\FormRequest;

class CustomerPasswordFormRequest extends FormRequest
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

        $rules = [];

        if ($this->route()->uri == 'api/admin/customers/set-new-password/{customer}') {
            $rules  =  [
                'password' => 'required|min:8',
            ];
        } else {

            $rules  =  [
                'password' => 'required|min:8',
                'password_confirmation' => 'required|same:password',
                'phone_number' => 'required|numeric',
                'idd' => 'required|numeric',
            ];
        }

        return  $rules;
    }
}
