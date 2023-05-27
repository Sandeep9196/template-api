<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountFormRequest extends FormRequest
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
            'bank_name' => $this->method() === 'POST' ? 'required|max:255': 'max:255',
            'account_name' => $this->method() === 'POST' ? 'required|max:255': 'max:255',
            'account_no' => $this->method() === 'POST' ? 'required|max:255': 'max:255',
            'account_type' => $this->method() === 'POST' ? 'required|max:255': 'max:255',
            'remark' => $this->method() === 'POST' ? 'nullable|max:255': 'max:255',
            'status' => $this->method() === 'POST' ? 'nullable|in:Active,In-active': 'nullable|in:Active,In-active',
        ];
    }
}
