<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemplateDetailRequest extends FormRequest
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
        $rules =   [
            'website_title' => empty($this->templateDetail->id) ? ['required', 'max:255'] : ['required', 'max:255'],
            'template_id' => 'required|exists:templates,id|numeric',
            'website_logo' => empty($this->templateDetail->id) ? ['required', 'image'] : ['nullable','image'],
            'h5_logo' => empty($this->templateDetail->id) ? ['required', 'image'] : ['nullable','image'],
            'website_description' => 'required',
            'website_title' => 'required',
            'theme' => 'required',
            'banner_style' => 'required',
            'company_info.company_name' => 'max:255',
            'company_info.address' => 'array',
            'company_info.address.address_line' => 'max:255',
            'company_info.address.country_id' => 'numeric|exists:countries,id',
            'company_info.address.state_id' => 'nullable|exists:states,id',
            'company_info.address.city_id' => 'nullable|exists:cities,id',
            'social.name' => 'nullable',
            'social.image' => 'nullable|image',
            'social.status' => 'nullable',
            'social.purpose' => 'nullable|in:0,1,2,3,4,5,6,7,8,9,10',
        ];
        return $rules;
    }
}
