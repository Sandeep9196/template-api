<?php

namespace App\Http\Requests;

use App\Models\Language;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionStatusFormRequest extends FormRequest
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
            'status' => $this->method() === 'POST' ? 'required|in:Success,Fail': 'required|in:Success,Fail',
            'transaction_ID' => $this->method() === 'POST' ? 'required|exists:transactions,transaction_ID': 'required|exists:transactions,transaction_ID',
        ];
    }

    public function messages()
    {
        return [
            'status' => $this->method() === 'POST' ? 'required|in:Success,Fail': 'required|in:Success,Fail',
            'transaction_ID.exists' => 'transaction_ID not valid',
        ];
    }
}
