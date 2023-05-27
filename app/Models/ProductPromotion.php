<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPromotion extends Model
{
    use HasFactory;
    protected $table="product_promotion";
    public $timestamps = false;
    //protected $hidden = ['deleted_at'];

    public function product(){
        return $this->belongsToMany(Product::class);
    }


}
