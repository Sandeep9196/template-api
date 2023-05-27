<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubCategoryFormRequest extends FormRequest
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
            'category_id' => $this->method() === 'POST' ? 'required|exists:categories,id': 'exists:categories,id',
            'slug' => $this->method() === 'POST' ? 'required|max:255|unique:sub_categories,slug,NULL,id,deleted_at,NULL':
                                                    "unique:sub_categories,slug,{$this->subCategory->id},id,deleted_at,NULL",
            'name' => $this->method() === 'POST' ? 'required': '',
            'description' => $this->method() === 'POST' ? 'nullable': '',
            'status' => $this->method() === 'POST' ? 'required|in:active,inactive': 'in:active,inactive',
            'file' => $this->method() === 'POST' ? 'required|file': 'file',
            'translation_name' => $this->method() === 'POST' ? 'required|array': 'array',
            'translation_desc' => $this->method() === 'POST' ? 'required|array': 'array',
            'translation_name.*.language_id' => $this->method() === 'POST' ? 'required|exists:languages,id': 'exists:languages,id',
            'translation_name.*.field_name' => $this->method() === 'POST' ? 'required': '',
            'translation_name.*.translation' => $this->method() === 'POST' ? 'required': '',
        ];
    }
}
