<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Address extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'country_id',
        'state_id',
        'city_id',
        'company_id',
        'address_line',
        'address_line_2',
        'pincode'
    ];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }
    public function toArray()
    {
        $attributes = parent::toArray();
        $translateData = array();
        if (request()->segment(2) == ADMIN) {
        Language::all()->each(function ($language) use (&$translateData) {
            $tran = $this->translations()->whereLanguageId($language->id)->get();
            $oneLanguageData = array();
            $tran->each(function ($t) use (&$oneLanguageData, &$language){
                $oneLanguageData[$t->field_name] = $t->translation;
            });
            if(sizeof($oneLanguageData) > 0){
                $oneLanguageData['address_line']??$oneLanguageData['address_line']='';
                    $oneLanguageData['address_line_2']??$oneLanguageData['address_line_2']='';
                $translateData[$language->locale] = $oneLanguageData;
            }else{
                $oneLanguageData['address_line']??$oneLanguageData['address_line']='';
                    $oneLanguageData['address_line_2']??$oneLanguageData['website_description']='';
                $translateData[$language->locale] = $oneLanguageData;
            }
        });
        } else{
            Language::whereId(request()->lang_id)->each(function ($language) use (&$translateData) {
                $tran = $this->translations()->whereLanguageId($language->id)->get();
                $oneLanguageData = array();
                $tran->each(function ($t) use (&$oneLanguageData){
                    $oneLanguageData[$t->field_name] = $t->translation;
                });
                if(sizeof($oneLanguageData) > 0){
                    $oneLanguageData['address_line']??$oneLanguageData['address_line']='';
                    $oneLanguageData['address_line_2']??$oneLanguageData['address_line_2']='';
                    $translateData = $oneLanguageData;
                }else{
                    $oneLanguageData['address_line']??$oneLanguageData['address_line']='';
                    $oneLanguageData['address_line_2']??$oneLanguageData['address_line_2']='';
                    $translateData= $oneLanguageData;
                }
            });
        }
        $attributes['translations'] = $translateData;
        return $attributes;
    }


}
