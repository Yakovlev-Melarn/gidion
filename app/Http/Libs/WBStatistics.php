<?php


namespace App\Http\Libs;


use App\Models\Seller;
use App\Models\User;
use App\Models\Wbststock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WBStatistics
{
    public static function stocks(Seller $seller)
    {
        $dateFrom = date("Y-m-d", strtotime('-90 days'));
        $response = Http::withHeaders([])->acceptJson()->withToken($seller->apiKey)->get("https://statistics-api.wildberries.ru/api/v1/supplier/stocks", [
            'dateFrom' => $dateFrom
        ]);
        return $response->object();
    }

    public static function sales(Seller $seller)
    {
        $dateFrom = date("Y-m-d", strtotime('-1 days'));
        $response = Http::withHeaders([])->acceptJson()->withToken($seller->apiKey)->get("https://statistics-api.wildberries.ru/api/v1/supplier/sales", [
            'dateFrom' => $dateFrom
        ]);
        return $response->json();
    }

    public static function orders(Seller $seller)
    {
        $dateFrom = date("Y-m-d", strtotime('-1 days'));
        $response = Http::withHeaders([])->acceptJson()->withToken($seller->apiKey)->get("https://statistics-api.wildberries.ru/api/v1/supplier/orders", [
            'dateFrom' => $dateFrom
        ]);
        return $response->json();
    }
}
