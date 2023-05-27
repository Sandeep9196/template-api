<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class State extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at','is_bot'];

    protected $with = ['country'];

    public function country(){
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function city()
    {
        return $this->HasMany(City::class);
    }

    public function translates()
    {
        return $this->morphOne(Translation::class, 'translationable');
    }

    public function getStateNameAttribute($attribute)
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
