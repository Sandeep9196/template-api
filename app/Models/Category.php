<?php

namespace App\Models;

use App\Traits\DateSerializable;
use App\Traits\RelationDeleteRestoreable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use OwenIt\Auditing\Contracts\Auditable;

class Category extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DateSerializable, RelationDeleteRestoreable;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    protected $with = ['image:id,path,fileable_id', 'translates'];
    public $relationsToCascade = ['image', 'products', 'subCategories', 'translates'];
    //protected $hidden = ['deleted_at'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
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
        $promotionalData = Session::get("query_promotions_session");
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
                    $oneLanguageData['description'] = '';
                    $translateData[$language->locale] = $oneLanguageData;
                }
            });
        }else{
            Language::whereId(request()->lang_id)->each(function ($language) use (&$translateData, $promotionalData) {
                $tran = $this->translates()->whereLanguageId($language->id)->get();
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
        $attributes['translates'] = $translateData;
        return $attributes;
    }
}
