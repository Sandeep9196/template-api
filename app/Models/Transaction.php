<?php

namespace App\Models;

use App\Traits\DateSerializable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Lang;
use Locale;
use OwenIt\Auditing\Contracts\Auditable;

class Transaction extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DateSerializable;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    public function image()
    {
        return $this->morphOne(File::class, 'fileable');
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function customer()
    {
        return $this->hasOne(Customer::class,'id','member_id');
    }
    // public function withdraw()
    // {
    //     return $this->hasOne(WithdrawDetail::class);
    // }
    // public function account()
    // {
    //     return $this->hasOne(BankAccount::class,'id','bank_account_id');
    // }
    public function getMessageAttribute($attribute){
        if($attribute){
            preg_match_all('/{{(.*?)}}/', $attribute, $matches);
            $matches = $matches[1];
            foreach($matches as $key => $match){
                $translation = trans('message.'.$match);
                $attribute = str_replace("{{".$match."}}", $translation, $attribute);
            }
        }
        return $attribute;
    }
}
