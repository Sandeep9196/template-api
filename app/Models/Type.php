<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    public $relationsToCascade = ['translations'];
    protected $fillable = [
        'name'
    ];
    use HasFactory;

    public function group()
    {
        return $this->hasMany(Group::class);
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function menu()
    {
        return $this->hasMany(Menu::class);
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
                $oneLanguageData['name']??$oneLanguageData['name']='';
                $translateData[$language->locale] = $oneLanguageData;
            }else{
                $oneLanguageData['name']??$oneLanguageData['name']='';
                $translateData[$language->locale] = $oneLanguageData;
            }
        });
    }else{
        Language::whereId(request()->lang_id)->each(function ($language) use (&$translateData) {
            $tran = $this->translations()->whereLanguageId($language->id)->get();
            $oneLanguageData = array();
            $tran->each(function ($t) use (&$oneLanguageData, &$language){
                $oneLanguageData[$t->field_name] = $t->translation;
            });
            if(sizeof($oneLanguageData) > 0){
                $oneLanguageData['name']??$oneLanguageData['name']='';
                $translateData= $oneLanguageData;
            }else{
                $oneLanguageData['name']??$oneLanguageData['name']='';
                $translateData = $oneLanguageData;
            }
        });
    }
        $attributes['translations'] = $translateData;
        return $attributes;
    }
}
