<?php

namespace App\Http\Requests;

use App\Models\Language;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class LanguageFormRequest extends FormRequest
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
        $translationPurpose = implode(',',TRANSLATION_PURPOSE[Language::class]);
        return [
            'name' => $this->method() === 'POST' ? 'required|unique:languages,name'
            : 'required|unique:languages,name,'.$this->language->id,
            'locale' => $this->method() === 'POST' ? 'required|unique:languages,locale'
            : 'required|unique:languages,locale,'.$this->language->id,
            'locale_web' => $this->method() === 'POST' ? 'required|unique:languages,locale_web'
            : 'required|unique:languages,locale_web,'.$this->language->id,

        ];
    }
}
