<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckBalanceController extends Controller
{

    public function getBalance()
    {
        $client = new \SoapClient(env('SMS_GATEWAY_URL'));
        $params = array(
            "User" => env('SMS_GATEWAY_USERID'),
            "Password" => env('SMS_GATEWAY_PASSWORD'),
            "CPCode" => env('SMS_GATEWAY_CODE')
        );
        $response = $client->__soapCall("checkBalance", array($params));
        $result['message'] = 'check_balance_successfully';
        $result['data'] = $response->return->balance;
        $result['statusCode'] = 200;
        return getSuccessMessages($result);
    }
}
