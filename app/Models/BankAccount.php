<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class BankAccount extends Model implements Auditable
{
    use  SoftDeletes, DateSerializable;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    public function customer(){
        return $this->belongsTo(Customer::class,'member_id','id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}
