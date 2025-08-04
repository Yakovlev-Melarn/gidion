<?php


namespace App\Http\Libs;


use App\Models\Card;
use App\Models\Seller;
use Illuminate\Support\Facades\Http;

class WBDiscount
{
    public static function update(Seller $seller, Card $card)
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://discounts-prices-api.wildberries.ru/api/v2/upload/task", [
                'data' => [[
                    'nmID' => (int)$card->nmID,
                    'discount' => (int)$card->prices->discount
                ]]
            ]);
        print_r($response->json());
    }

    public static function remove(Seller $seller, Card $card)
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://discounts-prices-api.wildberries.ru/api/v2/upload/task", ['data' => [['nmID' => (int)$card->nmID, 'discount' => 0]]]);
        echo $response->status() . "\r\n";
    }
}
