<?php

namespace App\Http\Requests;

use App\Rules\ExternalSystemRoleRule;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class DealFormRequest extends FormRequest
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
        $rules = [];

        if ($this->is('api/admin/deals/set-deal') == true) {
            $rules = [
                'deal_id' => 'required|numeric|integer',
                'time_period' => 'required|numeric|integer',
            ];
        }
        if($this->route()->uri == 'api/admin/deals/{deals}'){
            $rules = [
                'product_id' => 'required|exists:products,id',
                'slot_price' => 'nullable',
                'deal_price' => 'nullable',
                'status' => 'nullable|in:active,inactive,settled',
            ];
        }
        if($this->is('api/admin/deals') == true){
            $rules = [
                'product_id' => 'required|exists:products,id',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        $message = [];
        if ($this->is('api/admin/deals/set-deal') == true) {
            $message = [
                'deal_id.required' => 'Deal id required',
                'time_period.required' => 'Time period required'
            ];
        }

        if($this->is('api/admin/deals') == true){
            $message = [
                'product_id.required' => 'Product id required',
            ];
        }
        return $message;
    }
}
