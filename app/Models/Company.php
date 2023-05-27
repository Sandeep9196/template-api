<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = ['id'];
    protected $with = ['address'];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

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
                $oneLanguageData['company_name']??$oneLanguageData['company_name']='';
                    $oneLanguageData['copy_right']??$oneLanguageData['copy_right']='';
                $translateData[$language->locale] = $oneLanguageData;
            }else{
                $oneLanguageData['company_name']??$oneLanguageData['address_line']='';
                    $oneLanguageData['copy_right']??$oneLanguageData['copy_right']='';
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
                    $oneLanguageData['company_name']??$oneLanguageData['company_name']='';
                    $oneLanguageData['copy_right']??$oneLanguageData['copy_right']='';
                    $translateData = $oneLanguageData;
                }else{
                    $oneLanguageData['company_name']??$oneLanguageData['company_name']='';
                    $oneLanguageData['copy_right']??$oneLanguageData['copy_right']='';
                    $translateData= $oneLanguageData;
                }
            });
        }
        $attributes['translations'] = $translateData;
        return $attributes;
    }
}
