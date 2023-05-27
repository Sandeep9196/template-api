<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Template extends Model implements Auditable
{
    use  SoftDeletes, DateSerializable;
     use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    public function image()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    public function translates()
    {
        return $this->morphMany(Translation::class, 'translationable')->where('language_id', request()->lang_id);
    }

}
