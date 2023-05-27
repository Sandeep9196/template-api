<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Tag extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DateSerializable;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    protected $with = ['image', 'translates'];
    public $relationsToCascade = ['image','products','translates'];

    //protected $hidden = ['deleted_at','is_bot','pivot'];


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
        return $attributes;
    }

}
