<?php


namespace App\Http\Libs;


use Illuminate\Support\Facades\Http;

class SamsonLib
{
    public static function getProductStock($sku)
    {
        $url = "https://api.samsonopt.ru/v1/sku/{$sku}/stock?api_key=635dba79ee799d90ce09e82e1f659c0f";
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
        $result = Http::withHeader('User-Agent', $agent)->get($url);
        return $result->json();
    }
}
