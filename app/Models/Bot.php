<?php

namespace App\Models;

use App\Traits\DateSerializable;
use App\Traits\RelationDeleteRestoreable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Bot extends Model implements Auditable
{
    use HasFactory, DateSerializable, SoftDeletes, RelationDeleteRestoreable;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at'];

    public function botGlobalConfiguration(){
        return $this->morphOne(Configure::class, 'configurable');
    }

}
