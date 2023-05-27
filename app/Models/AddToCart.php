<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AddToCart extends Model implements Auditable
{
    use HasFactory, DateSerializable;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at'];

}
