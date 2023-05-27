<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Country extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    public $relationsToCascade = ['states'];
    protected $with = ['translates'];
    //protected $hidden = ['deleted_at'];

     public function states()
    {
        return $this->HasMany(State::class);
    }

    public function city()
    {
        return $this->HasMany(City::class);
    }

    public function getFlagUrlAttribute($attribute){

        return url('/').'/'.$attribute;

    }

    public function translates()
    {
        return $this->morphOne(Translation::class, 'translationable');
    }

    public function getNameAttribute($attribute)
    {
        $languageId = 1;
        $locale = request('locale');
        if($locale){
            $language = Language::whereLocaleWeb($locale)->first();
            if($language) $languageId = $language->id;
        }

        $translation = $this->translates()->whereLanguageId($languageId)->whereFieldName('name')->first();
        if ($translation)
            return $translation->translation;

        $modifiedAttributes = trans('locations.'.$attribute);

        if($modifiedAttributes){
            if(!preg_match('/locations/',$modifiedAttributes))
                $attribute = $modifiedAttributes;
        }
        return $attribute;
    }


    public function toArray()
    {

        $attributes = parent::toArray();
        $translateData = array();
        Language::all()->each(function ($language) use (&$translateData) {
            $tran = $this->translates()->whereLanguageId($language->id)->get();
            $oneLanguageData = array();
            $tran->each(function ($t) use (&$oneLanguageData, &$language){
                $oneLanguageData[$t->field_name] = $t->translation;
            });
            if(sizeof($oneLanguageData) > 0)
                $translateData[$language->locale] = $oneLanguageData;
            else{
                $oneLanguageData['name'] = "";
                $translateData[$language->locale] = $oneLanguageData;
            }
        });
        $attributes['translates'] = $translateData;
        return $attributes;
    }

}
