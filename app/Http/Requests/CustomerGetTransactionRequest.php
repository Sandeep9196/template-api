<?php

namespace App\Http\Requests;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class CustomerGetTransactionRequest extends FormRequest
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
            'transaction_type' =>'nullable|in:transfer_in,transfer_out,withdraw',
            'rowsPerPage' =>'nullable|int',
            'sortBy' =>'nullable',
            'descending' =>'nullable',
            'page' =>'nullable|int',
            'date_range' => ['nullable', 'string', 'regex:/^\d{4}-\d{2}-\d{2}\s-\s\d{4}-\d{2}-\d{2}$/'],

        ];
    }
    public function messages()
    {
        return [
            'date_range.regex' => 'The date range format is invalid. Must follow this formate:yyyy-mm-dd - yyyy-mm-dd'
        ];
        
    }
}
