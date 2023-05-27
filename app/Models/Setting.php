<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Setting extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    protected $with = ['images','translations'];
    //protected $hidden = ['deleted_at'];

    public function value(): Attribute
    {
        return Attribute::make(
            get:fn ($value) => $this->getValue($value),
            set:fn ($value) => is_array($value) ? json_encode($value) : $value,
        );
    }

    public function getValue($value)
    {
        if (is_array(json_decode($value))) {
            return json_decode($value);
        }

        if (is_numeric($value)) {
            return is_int($value) ? (int) $value : (float) $value;
        }

        return $value;
    }

    public function images()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function toArray()
    {
        $attributes = parent::toArray();
        $translateData = array();
        Language::all()->each(function ($language) use (&$translateData) {
            $tran = $this->translations()->whereLanguageId($language->id)->get();
            $oneLanguageData = array();
            $tran->each(function ($t) use (&$oneLanguageData, &$language){
                $oneLanguageData[$t->field_name] = $t->translation;
            });
            if(sizeof($oneLanguageData) > 0){
                $oneLanguageData['contact_address1']??$oneLanguageData['contact_address1'] = '';
                $oneLanguageData['contact_address2']??$oneLanguageData['contact_address2']='';
                $oneLanguageData['contact_city']??$oneLanguageData['contact_city']='';
                $oneLanguageData['contact_province']??$oneLanguageData['contact_province']='';
                $oneLanguageData['contact_country']??$oneLanguageData['contact_country']='';
                $translateData[$language->locale] = $oneLanguageData;
            }
            else{
                $oneLanguageData['contact_address1'] = '';
                $oneLanguageData['contact_address2'] = '';
                $oneLanguageData['contact_city'] = '';
                $oneLanguageData['contact_province'] = '';
                $oneLanguageData['contact_country'] = '';
                $translateData[$language->locale] = $oneLanguageData;
            }

        });
        $attributes['translations'] = $translateData;
        return $attributes;
    }
}
