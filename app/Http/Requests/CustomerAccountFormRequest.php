<?php

namespace App\Http\Requests;

use App\Traits\PhoneNumberSerializable;
use Illuminate\Foundation\Http\FormRequest;

class CustomerAccountFormRequest extends FormRequest
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
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'display_name' => 'nullable',
            'email' => 'nullable|email',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable',
            'new_password_confirmation' => 'required_with:new_password|same:new_password',
        ];
    }
}
