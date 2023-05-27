<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Currency extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use DateSerializable;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at'];

}
