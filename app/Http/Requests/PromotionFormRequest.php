<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionFormRequest extends FormRequest
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
            'name' => $this->method() === 'POST' ? 'nullable|max:255': 'max:255',
            'status' => $this->method() === 'POST' ? 'nullable|in:active,inactive': 'in:active,inactive',
            'slug' => $this->method() === 'POST' ? 'required|max:255|unique:promotions,slug,NULL,id,deleted_at,NULL': "slug,{$this->promotion->id},id,deleted_at,NULL",
            'translation' => $this->method() === 'POST' ? 'nullable|array': 'array',
            'translation.*.language_id' => $this->method() === 'POST' ? 'required|exists:languages,id': 'required|exists:languages,id',
            'translation.*.field_name' => $this->method() === 'POST' ? 'required|in:name': 'required|in:name',
            'translation.*.translation' => $this->method() === 'POST' ? 'required': 'required'
        ];
    }
}
