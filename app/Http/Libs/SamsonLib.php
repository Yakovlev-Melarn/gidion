<?php


namespace App\Http\Libs;


use Illuminate\Support\Facades\Http;

class SamsonLib
{
    private static string $apiKey = 'c63363e9e46de524234f80de15711aee';

    public static function getProductPrice($sku)
    {
        $url = "https://api.samsonopt.ru/v1/sku/{$sku}/price?api_key=" . self::$apiKey;
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
        $result = Http::withHeaders([
            'User-Agent' => $agent,
            'Accept' => 'application/json',
            'Accept-Encoding' => 'gzip'
        ])->get($url);
        return $result->json();
    }

    public static function getProductStock($sku)
    {
        $url = "https://api.samsonopt.ru/v1/sku/{$sku}/stock?api_key=" . self::$apiKey;
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
        $result = Http::withHeaders([
            'User-Agent' => $agent,
            'Accept' => 'application/json',
            'Accept-Encoding' => 'gzip'
        ])->get($url);
        return $result->json();
    }

    public static function getProducts($url = null)
    {
        if (empty($url)) {
            $url = "https://api.samsonopt.ru/v1/sku?api_key=" . self::$apiKey;
        }
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
        $result = Http::withHeaders([
            'User-Agent' => $agent,
            'Accept' => 'application/json',
            'Accept-Encoding' => 'gzip'
        ])->get($url);
        return $result->json();
    }
}
