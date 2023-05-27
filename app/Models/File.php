<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
class File extends Model implements Auditable
{
    use HasFactory, DateSerializable, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at','fileable_id','fileable_type'];

    public function fileable()
    {
        return $this->morphTo();
    }

    // public function getPathAttribute($attribute){
    //     $paths = explode('/', $attribute);
    //     if($paths[0] === 'public')
    //         return url('/').'/api/media/'.$attribute;
    //     else
    //         return url('/').'/'.$attribute;

    // }
    public function getPathAttribute($attribute){
        $paths = explode('/', $attribute);
        if($paths[0] === 'public')
            return env('MLM_URL').'/api/media/'.$attribute;
        else
            return env('MLM_URL').'/'.$attribute;
    }

    public function getStoragePathAttribute($attribute){

        return str_replace( url('/').'/api/media/','',$this->path);

    }


    public function toArray()
    {
        $attributes = parent::toArray();
        @FILE_PERPOSE_TYPES[$attributes['purpose']]?$attributes['purpose_string'] = FILE_PERPOSE_TYPES[$attributes['purpose']]:'';
        return $attributes;
    }

}
