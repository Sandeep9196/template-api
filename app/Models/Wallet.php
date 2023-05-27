<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Wallet extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DateSerializable;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = ['id'];


    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    public function toArray()
    {
        $attributes = parent::toArray();
        $attributes['currency_code'] = @$this->currency->code;
        return $attributes;
    }
}
