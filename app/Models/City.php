<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class City extends Model implements Auditable
{
    use HasFactory, DateSerializable, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at'];
    protected $with = [ 'translates'];

    public function state(){
        return $this->belongsTo(State::class);
    }
    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function translates()
    {
        return $this->morphOne(Translation::class, 'translationable');
    }

    public function getCityNameAttribute($attribute)
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
