<?php

namespace App\Http\Requests;

use App\Rules\ExternalSystemRoleRule;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class ProductFormRequest extends FormRequest
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
            'translation' => 'array|required',
            'sku' => $this->method() === 'POST' ? 'required|unique:products,sku' : 'required',
            'quantity' => 'required',
            'slug' =>  $this->method() === 'POST' ? 'required|unique:products,slug' : 'required',
            'price' => 'required',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required',
            'sku.required' => 'Product sku is required',
            'sku.unique' => 'Product sku must be qunique',
            'slug.unique' => 'Product slug must be qunique',
            'price.required' => 'required',
        ];
    }
}
