<?php

namespace App\Http\Requests;

use App\Models\Language;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawFormRequest extends FormRequest
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
        return [
            'bank_account_id' => $this->method() === 'POST' ? 'required|exists:bank_accounts,id': 'exists:bank_accounts,id',
            'amount' => $this->method() === 'POST' ? 'required': 'required',
        ];
    }
}
