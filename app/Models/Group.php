<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $with = ['type', 'translation'];

    public function type()
    {
        return $this->belongsTo(Type::class);
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
        if (request()->segment(2) == ADMIN) {
        Language::all()->each(function ($language) use (&$translateData) {
            $tran = $this->translates()->whereLanguageId($language->id)->get();
            $oneLanguageData = array();
            $tran->each(function ($t) use (&$oneLanguageData, &$language) {
                $oneLanguageData[$t->field_name] = $t->translation;
            });
            if (sizeof($oneLanguageData) > 0)
                $translateData[$language->locale] = $oneLanguageData;
            else {
                $oneLanguageData['name'] = "";
                $translateData[$language->locale] = $oneLanguageData;
            }
        });
    }else{
        Language::whereId(request()->lang_id)->each(function ($language) use (&$translateData) {
            $tran = $this->translates()->whereLanguageId($language->id)->get();
            $oneLanguageData = array();
            $tran->each(function ($t) use (&$oneLanguageData, &$language) {
                $oneLanguageData[$t->field_name] = $t->translation;
            });
            if (sizeof($oneLanguageData) > 0)
                $translateData = $oneLanguageData;
            else {
                $oneLanguageData['name'] = "";
                $translateData= $oneLanguageData;
            }
        });
    }

        $attributes['translations'] = $translateData;
        return $attributes;
    }
    public function menu()
    {
        return $this->hasMany(Menu::class);
    }
}
