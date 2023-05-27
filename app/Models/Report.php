<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Report extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    // protected $guarded = ['id'];

    // protected $with = ['country'];

    // public function country(){
    //     return $this->hasOne(Country::class, 'id', 'country_id');
    // }
}
