<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Customer;


class Invoice extends Model
{
   use HasFactory, SoftDeletes;
   use \Staudenmeir\EloquentHasManyDeep\HasTableAlias;
   use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

   protected $guarded = ['id'];
   //protected $hidden = ['deleted_at'];

   public function orderProduct()
   {
       return $this->hasMany(OrderProduct::class,'order_id','order_id');
   }

   public function customer()
    {
        return $this->hasOneDeepFromReverse(
            (new Customer())->invoice()
        );
    }

   public function order()
   {
       return $this->belongsTo(Order::class);
   }
}
