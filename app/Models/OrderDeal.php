<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use stdClass;

class OrderDeal extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function slotDeals()
    {
        return $this->hasMany(SlotDeal::class, 'order_id',  'order_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
            $slotDeals = [];
            foreach ($this->slotDeals as $sd) {
                if ($sd->deal->product_id === $this->product_id)
                    array_push($slotDeals, $sd);
                $productData['deal'] = $sd->deal;
            }
            $productData['slot_deals'] = $slotDeals;
            $productData['slot_deals_count'] = count($slotDeals);
            $attributes['product_slot_deals'] = $productData;
            $attributes['order_table_id'] = $this->order->order_id;
        }

        return $attributes;
    }
}
