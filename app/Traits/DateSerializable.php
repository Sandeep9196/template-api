<?php

namespace App\Traits;

trait DateSerializable
{
    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
