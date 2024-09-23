<?php


namespace App\Http\Libs;


use App\Models\Seller;
use Illuminate\Support\Facades\Http;

class WBMarketplace
{
    public static function stocksUpdate(Seller $seller, $stocks)
    {
        $result = Http::withHeaders([])
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->put("https://suppliers-api.wildberries.ru/api/v3/stocks/{$seller->whID}", [
                'stocks' => $stocks
            ]);
        if (!empty($result = $result->json())) {
            if(!empty($result)) {
                print_r($result);
                print_r($stocks);
            }
        }
    }

    public static function stockUpdate(Seller $seller, $stock, $barcode)
    {
        self::stocksUpdate($seller, [
            [
                'sku' => (string)$barcode,
                'amount' => (int)$stock
            ]
        ]);
    }

    public static function createSupplies(Seller $seller)
    {
        Http::withHeaders([])
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://suppliers-api.wildberries.ru/api/v3/supplies", [
                'name' => date("Y-m-d H:i:s")
            ]);
    }

    public static function getQrCode(Seller $seller, $orders)
    {
        $result = Http::withHeaders([])
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://suppliers-api.wildberries.ru/api/v3/orders/stickers?type=svg&width=58&height=40", ['orders' => $orders]);
        return $result->json();
    }

    public static function addOrderToShipment(Seller $seller, $orderId, $shipmentId)
    {
        $result = Http::withHeaders([])
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->patch("https://suppliers-api.wildberries.ru/api/v3/supplies/{$shipmentId}/orders/{$orderId}");
        return $result->status();
    }

    public static function getNewOrders(Seller $seller)
    {
        $result = Http::withHeaders([])
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->get("https://suppliers-api.wildberries.ru/api/v3/orders/new");
        return $result->json();
    }

    public static function getOrders(Seller $seller, $supplyId)
    {
        $result = Http::withHeaders([])
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->get("https://suppliers-api.wildberries.ru/api/v3/supplies/{$supplyId}/orders");
        return $result->json();
    }

    public static function getOpenSupplies(Seller $seller)
    {
        $result = Http::withHeaders([])
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->get("https://suppliers-api.wildberries.ru/api/v3/supplies", [
                'limit' => 1000,
                'next' => 0
            ]);
        $result = $result->json();
        $openShipment = false;
        foreach ($result['supplies'] as $shipment) {
            if (!$shipment['done']) {
                $openShipment = $shipment;
            }
        }
        return $openShipment;
    }
}
