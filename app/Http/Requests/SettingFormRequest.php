<?php

namespace App\Http\Requests;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class SettingFormRequest extends FormRequest
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

            'contact_zip'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'contact_phone1'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'contact_phone2'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'admin_email_id'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'general_email_id'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'contact_email_id'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'website_name'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'meta_title' => $this->method() === 'POST' ? 'nullable|max:100':'nullable|max:100',
            'meta_description'=> $this->method() === 'POST' ? 'nullable|max:200':'nullable|max:200',
            'meta_keywords'=> $this->method() === 'POST' ? 'nullable|max:200':'nullable|max:200',
            'instagram_link'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'instagram_status' => $this->method() === 'POST' ? 'nullable|in:on,off':'nullable|in:on,off',
            'youtube_link'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'youtube_status'=> $this->method() === 'POST' ? 'nullable|in:on,off':'nullable|in:on,off',
            'facebook_link'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'facebook_status' => $this->method() === 'POST' ? 'nullable|in:on,off':'nullable|in:on,off',
            'pinterest_link'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'pinterest_status' => $this->method() === 'POST' ? 'nullable|in:on,off':'nullable|in:on,off',
            'twitter_link'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'twitter_status' => $this->method() === 'POST' ? 'nullable|in:on,off':'nullable|in:on,off',
            'qq_link'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'qq_status' => $this->method() === 'POST' ? 'nullable|in:on,off':'nullable|in:on,off',
            'skype_link'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'skype_status' => $this->method() === 'POST' ? 'nullable|in:on,off':'nullable|in:on,off',
            'telegram_link'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'telegram_status' => $this->method() === 'POST' ? 'nullable|in:on,off':'nullable|in:on,off',
            'whatsapp_link'=> $this->method() === 'POST' ? 'nullable|max:191':'nullable|max:191',
            'whatsapp_status' => $this->method() === 'POST' ? 'nullable|in:on,off':'nullable|in:on,off',
            'logo' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'instagram_icon' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'youtube_icon' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'facebook_icon' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'pinterest_icon' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'twitter_icon' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'qq_icon' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'skype_icon' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'telegram_icon' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'whatsapp_icon' => $this->method() === 'POST' ? 'nullable|file':'nullable|file',
            'translation' => $this->method() === 'POST' ? 'nullable|array': 'nullable|array',
            'translation.*.language_id' => $this->method() === 'POST' ? 'required|exists:languages,id': 'exists:languages,id',
            'translation.*.field_name' => $this->method() === 'POST' ? 'required|in:contact_address1,contact_address2,contact_city,contact_province,contact_country': '',
            'translation.*.translation' => $this->method() === 'POST' ? 'required': '',
        ];
    }
}
