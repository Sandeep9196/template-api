<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipping extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at'];

    public function shippingLogs(){
        return $this->hasMany(ShippingLog::class);
    }

    public function deliveredProducts()
    {
        return $this->hasManyDeepFromReverse(
            (new Product())->shipping()->where('shippings.status','Delivered')
        );
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

}
