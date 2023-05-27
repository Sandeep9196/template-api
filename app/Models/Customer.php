<?php

namespace App\Models;

use App\Traits\DateSerializable;
use App\Traits\RelationDeleteRestoreable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

class Customer extends Authenticatable implements Auditable
{
    use HasRoles, HasApiTokens, HasFactory, SoftDeletes, HasApiTokens, DateSerializable, RelationDeleteRestoreable,Notifiable;
    use \OwenIt\Auditing\Auditable;
    use \Staudenmeir\EloquentHasManyDeep\HasTableAlias;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    protected $guarded = ['id'];

    protected $hidden = ['deleted_at','password'];


    public $relationsToCascade = ['addresses','orders','invoice'];

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    protected function setPasswordAttribute($value)
    {
        return $this->attributes['password'] = Hash::make($value);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class)->where('status','!=', 'reserved');
    }



    public function earnings()
    {
        return $this->belongsTo(Order::class);
    }


    public function order() {
        return $this->hasMany(OrderProduct::class)->where(function($q) {
            $q->where('status','confirmed')->orWhere('status','winner');
        });
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class,'member_id', 'id');
    }



}
