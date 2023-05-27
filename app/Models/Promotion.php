<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes, DateSerializable;

    protected $guarded = ['id'];
    protected $with = ['image', 'translates','translation'];
    //protected $hidden = ['deleted_at'];

    public function product(){
        return $this->belongsToMany(Product::class);
    }

    public function image()
    {
        return $this->morphOne(File::class, 'fileable');
    }
    public function translates()
    {
        return $this->morphOne(Translation::class, 'translationable');
    }

    public function translation()
    {
        return $this->morphMany(Translation::class, 'translationable')->where('language_id', request()->lang_id);
    }

    public function toArray()
    {

        $attributes = parent::toArray();
        $translateData = array();
        if(request()->segment(2) == ADMIN ){
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
        } else {
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
        }

        $attributes['translates'] = $translateData;
        $attributes['translation'] = $translateData;
        return $attributes;
    }

}
