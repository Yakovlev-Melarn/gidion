<?php


namespace App\Http\Libs;


use App\Models\Seller;
use Illuminate\Support\Facades\Http;

class WBPrice
{
    public static function pricesUpdate(Seller $seller, array $prices)
    {
        $result = Http::withHeaders([])
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://discounts-prices-api.wildberries.ru/api/v2/upload/task", $prices);
        print_r($result->json());
        return $result->json();
    }

    public static function priceList(Seller $seller, $offset, $nmId)
    {
        $url = "https://discounts-prices-api.wildberries.ru/api/v2/list/goods/filter?limit=1000&offset={$offset}";
        if (!empty($nmId)) {
            $url .= "&filterNmID={$nmId}";
        }
        $response = Http::withHeaders([])
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->get($url);
        $response = $response->json();
        return $response['data']['listGoods'];
    }
}
