<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TemplateDetail extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    public $relationsToCascade = ['translations'];

    public function templateConfiguration(){

        return $this->morphMany(Configure::class, 'configurable');
    }
    public function websiteLogo()
    {
        return $this->morphOne(File::class, 'fileable');
    }
    public function h5Logo()
    {
        return $this->morphOne(File::class, 'fileable')->wherePurpose(11);
    }
    public function socialLogo()
    {
        return $this->morphMany(File::class, 'fileable')->whereNotIn('purpose',[0,11]);
    }
    public function companyInfo()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
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
                $oneLanguageData['website_title']??$oneLanguageData['website_title']='';
                    $oneLanguageData['website_description']??$oneLanguageData['website_description']='';
                $translateData[$language->locale] = $oneLanguageData;
            }else{
                $oneLanguageData['website_title']??$oneLanguageData['website_title']='';
                    $oneLanguageData['website_description']??$oneLanguageData['website_description']='';
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
                    $oneLanguageData['website_title']??$oneLanguageData['website_title']='';
                    $oneLanguageData['website_description']??$oneLanguageData['website_description']='';
                    $translateData = $oneLanguageData;
                }else{
                    $oneLanguageData['website_title']??$oneLanguageData['website_title']='';
                    $oneLanguageData['website_description']??$oneLanguageData['website_description']='';
                    $translateData= $oneLanguageData;
                }
            });
        }
        $attributes['translations'] = $translateData;
        return $attributes;
    }
}
