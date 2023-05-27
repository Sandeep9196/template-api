<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $casts = [
        'data' => 'array',
        'id' => 'string'
    ];
    //protected $hidden = ['deleted_at'];

    public function getDataAttribute($value) {
        $value =  json_decode($value);
        
        $message = @$value->message;
        if($message){
            preg_match_all('/{{(.*?)}}/', $message, $matches);
            $matches = $matches[1];
            foreach($matches as $key => $match){
                $translation = trans('message.'.$match);
                $message = str_replace("{{".$match."}}", $translation, $message);
            }
        }
        $value->message = $message;
        return $value;
    }


}
