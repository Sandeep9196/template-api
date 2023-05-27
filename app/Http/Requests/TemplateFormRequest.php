<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemplateFormRequest extends FormRequest
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
            'name' =>
            empty($this->template->id) ?
                [
                    'required',
                    'max:255',
                    Rule::unique('templates')->using(function ($q) {
                        $q->where('status', 'active')->where('deleted_at', NULL);
                    }),
                ]
                :
                'required',
            'max:255',
            Rule::unique('templates')->using(function ($q) {
                $q->where('status', 'active')->where('deleted_at', NULL)->where('id', '!=', $this->template->id);
            }),
            'image' =>
            empty($this->template->id) ?
                [
                    'required',
                    'array'
                ] :
                [
                    'array',
                ],
            'image.*' => ['image']

        ];
        return $rules;
    }
}
