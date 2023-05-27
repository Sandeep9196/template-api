<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class Http
{

    public static function get($url)
    {
        $response = file_get_contents($url);
        return $response;
    }


    public static function post($url,$body) {
        $client = new Client();
        $response = $client->post($url, $body);
        return $response->getBody();
    }
}

