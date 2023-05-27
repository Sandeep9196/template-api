<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\RelationDeleteRestoreable;
use Illuminate\Support\Facades\DB;

class Product extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DateSerializable, RelationDeleteRestoreable;
    use \OwenIt\Auditing\Auditable;
    use \Staudenmeir\EloquentHasManyDeep\HasTableAlias;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    protected $guarded = ['id'];
    //protected $hidden = ['deleted_at'];

    protected $with = ['category', 'subCategory', 'prices', 'image:id,path,fileable_id', 'translations'];
    public $relationsToCascade = ['image', 'translation', 'prices'];

    public function category()
    {
        return $this->belongsTo(Category::class)->whereStatus('active');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class)->whereStatus('active');
    }

    public function image()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    public function productImage()
    {
        return $this->morphOne(File::class, 'fileable');
    }
    public function translation()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function prices()
    {
        if (request()->segment(2) == ADMIN) {
            return $this->hasMany(ProductCurrency::class, 'product_id', 'id');
        } else {
            return $this->hasMany(ProductCurrency::class, 'product_id', 'id')->whereCurrencyId(request()->cur_id);
        }
    }

    public function promotion()
    {
        return $this->belongsToMany(Promotion::class);
    }


    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(Order::class,);
    }

    public function translations()
    {
        return $this->hasMany(Translation::class, 'translationable_id', 'id')->where('translationable_type', 'App\Models\Product');
    }
    public function rating()
    {
        return $this->hasMany(Rating::class);
    }

    public function toArray()
    {
        $attributes = parent::toArray();
        $translateData = array();
        $promotionalData = Session::get("query_promotions_session");
        if (request()->segment(2) == ADMIN) {
            Language::all()->each(function ($language) use (&$translateData) {
                $tran = $this->translation()->whereLanguageId($language->id)->get();
                $oneLanguageData = array();
                $tran->each(function ($t) use (&$oneLanguageData, &$language) {
                    $oneLanguageData[$t->field_name] = $t->translation;
                    if ($t->field_name == 'name')
                        $oneLanguageData['id'] = $t->id;
                    else
                        $oneLanguageData['desc_id'] = $t->id;
                });
                if (sizeof($oneLanguageData) > 0)
                    $translateData[$language->locale] = $oneLanguageData;
                else {
                    $oneLanguageData['name'] = "";
                    $oneLanguageData['description'] = '';
                    $translateData[$language->locale_web] = $oneLanguageData;
                }
            });
        } else {
            Language::whereId(request()->lang_id)->each(function ($language) use (&$translateData, $promotionalData) {
                $tran = $this->translation()->whereLanguageId($language->id)->get();
                $oneLanguageData = array();
                $tran->each(function ($t) use (&$oneLanguageData, $promotionalData) {
                    if ($promotionalData  == true && $t->field_name == 'name') {
                        $oneLanguageData[$t->field_name] = $t->translation;
                    } elseif (!$promotionalData) {
                        $oneLanguageData[$t->field_name] = $t->translation;
                    }
                });
                if (sizeof($oneLanguageData) > 0)
                    $translateData  = $oneLanguageData;
                else {
                    $oneLanguageData['name'] = "";
                    $oneLanguageData['description'] = '';
                    $translateData  = $oneLanguageData;
                }
            });
        }
        $price = ProductCurrency::select('sale_price')->whereProductId($this->id)->whereCurrencyId(request()->cur_id)->first();
        $rating = Rating::selectRaw('ROUND(AVG(rate)) as rate')->whereProductId($this->id)->get();
        $attributes['rating'] =  !empty($rating[0]->rate) ? $rating[0]->rate : 0;
        $attributes['price'] = $price ? $price->sale_price : '0.00';
        $attributes['translations'] = $translateData;

        return $attributes;
    }
}
