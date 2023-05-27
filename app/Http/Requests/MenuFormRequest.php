<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class MenuFormRequest extends FormRequest
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

        $validation = [
            'slug' => $this->method() === 'POST' ? 'required|max:255|unique:banners,slug,NULL,id,deleted_at,NULL':
                                                    "unique:banners,slug,{$this->menu->id},id,deleted_at,NULL",
        ];

        return $validation;

    }
}
