<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Language extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DateSerializable;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at'];

    public function translates()
    {
        return $this->morphOne(Translation::class, 'translationable');
    }

    public function toArray()
    {
        $attributes = parent::toArray();
        $translateData = array();
        Language::all()->each(function ($language) use (&$translateData,$attributes) {
            // $tran = $this->translates()->whereLanguageId($language->id)->get();
            $tran = Translation::whereTranslationableType(Language::class)
                                    ->whereLanguageId($language->id)
                                    ->wherePurpose(getTranslationPurpose(Language::class,$attributes['id']))
                                    ->get();
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
