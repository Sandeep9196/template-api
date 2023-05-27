<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCurrency extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    //protected $with = ['curriency'];
    //protected $hidden = ['deleted_at'];

    public function currency(){
        return $this->belongsTo(Currency::class,'currency_id', 'id');
    }
}
