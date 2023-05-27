<?php

namespace App\Models;

use App\Traits\DateSerializable;
use App\Traits\RelationDeleteRestoreable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentGateway extends Model implements Auditable
{
    use SoftDeletes, DateSerializable, RelationDeleteRestoreable;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = ['id'];

    public function configurable(){
        return $this->morphOne(Configure::class, 'configurable');
    }


}
