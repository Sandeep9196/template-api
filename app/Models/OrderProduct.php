<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use stdClass;

class OrderProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_product';
    protected $guarded = ['id'];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function toArray()
    {
        $attributes = parent::toArray();
        $product = $this->product;
        if ($product) {
            $productData['product_id'] = $product->id;
            $productData['product_sku'] = $product->sku;
            $productData['slug'] = $product->slug;
            Session::put('product_order_id', $this->order_id);
            $attributes['order_table_id'] = $this->order->order_id;
        }

        return $attributes;
    }
}
