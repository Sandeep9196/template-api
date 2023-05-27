<?php

namespace App\Http\Requests;

use App\Models\Country;
use App\Rules\TranslationValidate;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class CountryFormRequest extends FormRequest
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
            'iso' => $this->method() === 'POST' ? 'required|max:255' : "max:255",
            'iso3' => $this->method() === 'POST' ? 'required|max:255' : "max:255",
            'idd' => $this->method() === 'POST' ? 'required|max:255' : "",
            'nationality' => $this->method() === 'POST' ? 'required|max:255' : "max:255",
            'translation_name' => $this->method() === 'POST' ? 'required|array': '',
            'translation_name.*.language_id' => $this->method() === 'POST' ? 'required|exists:languages,id': 'exists:languages,id',
            'translation_name.*.field_name' => $this->method() === 'POST' ? 'required': '',
            'translation_name.*.translation' =>$this->method() === 'POST' ?  new TranslationValidate(Country::class):'',
        ];
    }
}
