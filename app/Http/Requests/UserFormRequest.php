<?php

namespace App\Http\Requests;

use App\Rules\ExternalSystemRoleRule;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
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
        $emailRule = $this->method() === 'POST' ?
        'required|email:rfc|unique:users,email' : 'required|email:rfc|unique:users,email,'.$this->user->id;

        $passwordRule = $this->method() === 'POST' ? 'required' : 'nullable';

        return [
            'name' => 'required|regex:/^[A-Za-z0-9 ]*$/',
            'email' => $emailRule,
            'password' => $passwordRule,
            'role_id' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            // 'role_id.required' => 'Fields is required',
            // 'email.required' => 'Fields is required',
            // 'email.unique' => 'User ID already exist',
        ];
    }
}
