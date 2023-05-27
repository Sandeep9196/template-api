<?php

namespace App\Http\Requests;

use App\Traits\PhoneNumberSerializable;
use Illuminate\Foundation\Http\FormRequest;

class MemberFormRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'nullable',
            'display_name' => 'nullable',
            'email' => 'required',
            'phone_number' => $this->method() === 'POST' ? 'required|unique:customers,phone_number' : 'required',
            'idd' => $this->method() === 'POST' ? 'required' : 'required',
            'password' => $this->method() === 'POST' ? 'required' : 'nullable',
        ];
    }
}
