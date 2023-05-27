<?php

namespace App\Models;

use App\Traits\DateSerializable;
use App\Traits\RelationDeleteRestoreable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Menu extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DateSerializable, RelationDeleteRestoreable;
    use \OwenIt\Auditing\Auditable;

    public $relationsToCascade = ['image','translations'];
    protected $guarded = ['id'];
    protected $with = ['image'];
    //protected $hidden = ['deleted_at','fileable_id','fileable_type'];

    public function image()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
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
                $oneLanguageData['title']??$oneLanguageData['title']='';
                $oneLanguageData['description']??$oneLanguageData['description']='';
                $oneLanguageData['content']??$oneLanguageData['content']='';
                $translateData[$language->locale] = $oneLanguageData;
            }else{
                $oneLanguageData['title'] = "";
                $oneLanguageData['description'] = '';
                $oneLanguageData['content'] = '';
                $translateData[$language->locale] = $oneLanguageData;
            }
        });
        } else{
            Language::whereId(request()->lang_id)->each(function ($language) use (&$translateData) {
                $tran = $this->translations()->whereLanguageId($language->id)->get();
                $oneLanguageData = array();
                $tran->each(function ($t) use (&$oneLanguageData, &$language){
                    $oneLanguageData[$t->field_name] = $t->translation;
                });
                if(sizeof($oneLanguageData) > 0){
                    $oneLanguageData['title']??$oneLanguageData['title']='';
                    $oneLanguageData['description']??$oneLanguageData['description']='';
                    $oneLanguageData['content']??$oneLanguageData['content']='';
                    $translateData = $oneLanguageData;
                }else{
                    $oneLanguageData['title'] = "";
                    $oneLanguageData['description'] = '';
                    $oneLanguageData['content'] = '';
                    $translateData= $oneLanguageData;
                }
            });
        }
        $attributes['translations'] = $translateData;
        return $attributes;
    }
}
