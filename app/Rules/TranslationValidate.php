<?php

namespace App\Rules;

use App\Models\Translation;
use Illuminate\Contracts\Validation\Rule;

class TranslationValidate implements Rule
{
    protected $model;

    private $uniqueName = true;
    private $dataValue = "";
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // dd($value);
        $data = Translation::where('field_name', 'name')->where('translation', $value)->where('translationable_type',$this->model)->first();
        $this->dataValue = $value;
        if(!empty($data)){
            $this->uniqueName = false;
        }

        return $this->uniqueName;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Name already exists.';
    }
}
