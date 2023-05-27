<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory, DateSerializable;

    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at'];

}
