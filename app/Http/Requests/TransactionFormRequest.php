<?php

namespace App\Http\Requests;

use App\Models\Language;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class TransactionFormRequest extends FormRequest
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
            'member_id' => $this->method() === 'POST' ? 'nullable|exists:customers,id': 'exists:customers,id',
            'order_id' => $this->method() === 'POST' ? 'nullable|exists:orders,id': 'nullable|exists:orders,id',
            'transaction_type' => $this->method() === 'POST' ? 'required|in:transfer_in,transfer_out,withdraw':'in:transfer_in,transfer_out,withdraw',
            'amount' => $this->method() === 'POST' ? 'required|numeric': 'numeric',
            'currency_id' => $this->method() === 'POST' ? 'required|exists:currencies,id': 'exists:currencies,id',
            'message' => $this->method() === 'POST' ? 'nullable': 'nullable',
            'status' => $this->method() === 'POST' ? 'nullable|in:Debit,Credit,Reject,Review,Approve,Success,Fail': 'nullable|in:Debit,Credit,Reject,Review,Approve,Success,Fail',
            'redirect_url' => $this->method() === 'POST' ? 'nullable|url': 'nullable|url',
            'file' => $this->method() === 'POST' ? 'nullable|file': 'nullable|file',
        ];
    }
}
