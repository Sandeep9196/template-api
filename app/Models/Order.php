<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    use \Staudenmeir\EloquentHasManyDeep\HasTableAlias;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    protected $guarded = ['id'];

    protected $with = ['customer'];
    //protected $hidden = ['deleted_at'];

    protected $casts = [
        //'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];



    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product');
    }
    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class)->when(request()->status, function ($q) {
            return $q->where('status', request()->status)->whereOrderId($this->id);
        });
    }
    public function orderDeals()
    {
        return $this->hasMany(OrderDeal::class);
    }
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class,'order_id','id');
    }
}
