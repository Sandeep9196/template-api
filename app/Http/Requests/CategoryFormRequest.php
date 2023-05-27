<?php

namespace App\Http\Requests;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class CategoryFormRequest extends FormRequest
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
        $locales = Language::pluck('locale');
        $validation = [
            'slug' => $this->method() === 'POST' ? 'required|max:255|unique:categories,slug,NULL,id,deleted_at,NULL':
                                                    "unique:categories,slug,{$this->category->id},id,deleted_at,NULL",
            'status' => $this->method() === 'POST' ? 'required|in:active,inactive': 'in:active,inactive',
            'file' => $this->method() === 'POST' ? 'file': 'file',
            'translation_name' => $this->method() === 'POST' ? 'required|array': 'array',
            'translation_desc' => $this->method() === 'POST' ? 'required|array': 'array',
            'translation_name.*.language_id' => $this->method() === 'POST' ? 'required|exists:languages,id': 'exists:languages,id',
            'translation_name.*.field_name' => $this->method() === 'POST' ? 'required': '',
            'translation_name.*.translation' => $this->method() === 'POST' ? 'required': '',
            'name' => $this->method() === 'POST' ? 'required': '',
            'description' => $this->method() === 'POST' ? 'required': '',

        ];


        return $validation;
    }
}
