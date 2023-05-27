<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Translation extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    protected $with = ['language'];
    //protected $hidden = ['deleted_at'];

    public function translationable()
    {
        return $this->morphMany(Product::class, 'translationable');
    }

    public function language(){
        return $this->belongsTo(Language::class);
    }
}
