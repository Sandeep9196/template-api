<?php

namespace App\Http\Requests;

use App\Traits\FailedValidation;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class WinningRequest extends FormRequest
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

        $rules =   [];
        if ($this->route()->uri == 'api/admin/winners/create') {
            $rules =   [
                'deal_id' =>
                [
                    'required',
                    Rule::exists('deals', 'id')->where('status', '!=', 'settled'),
                ]

            ];
        }
        if ($this->route()->uri == 'api/admin/winners/generate-deal') {
            $rules =   [
                'deal_id' =>
                [
                    'required',
                    Rule::exists('deals', 'id')->where('status', 'settled'),
                ],
                'status' => 'required'

            ];
        }

        return $rules;
    }
    public function messages()
    {
        $message =  [];
        if ($this->route()->uri == 'api/admin/winners/create') {

            $message =  [
                'deal_id.required' => 'Deal id is required',
                'deal_id.exists' => 'Invalid Deal id',
            ];
        }
        if ($this->route()->uri == 'api/admin/winners/generate-deal') {

            $message =  [
                'deal_id.required' => 'Deal id is required',
                'deal_id.exists' => 'Deal not settled yet',
            ];
        }
        return $message;
    }
}
