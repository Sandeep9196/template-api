<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait PhoneNumberSerializable
{
    public function getValidatorInstance()
    {
        $this->formatPhoneNumber();

        return parent::getValidatorInstance();
    }

    protected function formatPhoneNumber()
    {
        $this->merge([
            'phone_number' => $this->phone_number?ltrim($this->phone_number,"0"):$this->phone_number
        ]);
        $this->merge([
            'idd' => $this->idd?formatIdd($this->idd):$this->idd
        ]);
    }
}
