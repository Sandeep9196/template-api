<?php

namespace App\Models;

use App\Traits\DateSerializable;
use App\Traits\RelationDeleteRestoreable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use OwenIt\Auditing\Contracts\Auditable;

class Banner extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DateSerializable, RelationDeleteRestoreable;
    use \OwenIt\Auditing\Auditable;

    public $relationsToCascade = ['image','products','subCategories','translations'];
    protected $guarded = ['id'];
    // protected $with = ['image:id,path,fileable_id'];
    //protected $hidden = ['deleted_at','fileable_id','fileable_type'];

    public function image()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    public function images()
    {
        $languageId = 1;
        $language = Language::find(request('lang_id'));
        $language? $languageId = $language->id:'';
        if($languageId == 1 || $language->local_web == 'en')
            return $this->morphOne(File::class, 'fileable')->wherePurpose($languageId)->orWhere('purpose',0);
        return $this->morphOne(File::class, 'fileable')->wherePurpose($languageId);

    }
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function toArray()
    {
        $attributes = parent::toArray();
        $translateData = array();
        $promotionalData = Session::get("query_promotions_session");
        if(request()->segment(2) == ADMIN ){
            Language::all()->each(function ($language) use (&$translateData) {
                $tran = $this->translations()->whereLanguageId($language->id)->get();
                $oneLanguageData = array();
                $tran->each(function ($t) use (&$oneLanguageData, &$language){
                    $oneLanguageData[$t->field_name] = $t->translation;
                });
                if(sizeof($oneLanguageData) > 0){
                    $oneLanguageData['name']??$oneLanguageData['name']='';
                    $oneLanguageData['description']??$oneLanguageData['description']='';
                    $translateData[$language->locale] = $oneLanguageData;
                }else{
                    $oneLanguageData['name'] = "";
                    $oneLanguageData['description'] = '';
                    $translateData[$language->locale] = $oneLanguageData;
                }
            });
        } else {
            Language::whereId(request()->lang_id)->each(function ($language) use (&$translateData, $promotionalData) {
                $tran = $this->translations()->whereLanguageId($language->id)->get();
                $oneLanguageData = array();
                $tran->each(function ($t) use (&$oneLanguageData,$promotionalData) {
                    if ($promotionalData  == true && $t->field_name == 'name') {
                        $oneLanguageData[$t->field_name] = $t->translation;
                    } elseif (!$promotionalData) {
                        $oneLanguageData[$t->field_name] = $t->translation;
                    }
                });
                if (sizeof($oneLanguageData) > 0)
                    $translateData  = $oneLanguageData;
                else {
                    $oneLanguageData['name'] = "";
                    $oneLanguageData['description'] = '';
                    $translateData  = $oneLanguageData;
                }
            });
        }

        $attributes['translations'] = $translateData;
        return $attributes;
    }
}
