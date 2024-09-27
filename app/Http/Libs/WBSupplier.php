<?php


namespace App\Http\Libs;


use Illuminate\Support\Facades\Http;

class WBSupplier
{
    public static function getDetail($nmId): array
    {
        $result = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->get("https://card.wb.ru/cards/v1/detail?appType=1&curr=rub&dest=123585811&spp=27&nm={$nmId}");
        return $result->json();
    }

    public static function getAmounts(string $nmIds): array|bool
    {
        $result = false;
        $response = self::getDetail($nmIds);
        if (isset($response['data']['products'])) {
            foreach ($response['data']['products'] as $productDetail) {
                $result[$productDetail['id']] = self::calcAmount($productDetail) > 6 ? 5 : 0;
            }
        }
        return $result;
    }

    private static function calcAmount(array $productDetail): int
    {
        $amount = 0;
        if (!empty($productDetail['sizes'][0]['stocks'])) {
            foreach ($productDetail['sizes'][0]['stocks'] as $stock) {
                    $amount += $stock['qty'];
            }
        }
        return $amount;
    }

    public static function getAmount(int $nmId): int
    {
        $response = self::getDetail($nmId);
        if (!isset($response['data']['products'])) {
            return 0;
        }
        $amount = self::calcAmount($response['data']['products'][0]);
        if ($amount > 6) {
            return 6;
        }
        return 0;
    }

    public static function getPrices(string $nmId): array|false
    {
        $response = self::getDetail($nmId);
        $result = false;
        if (!empty($response['data']['products'])) {
            foreach ($response['data']['products'] as $product) {
                $result[$product['id']] = $product['salePriceU'] / 100;
            }
            return $result;
        }
        return false;
    }

    public static function getPrice(int $nmId): int|bool
    {
        $response = self::getDetail($nmId);
        if (!empty($response['data']['products'][0]['salePriceU'])) {
            return (int)$response['data']['products'][0]['salePriceU'] / 100;
        }
        return false;
    }

    public static function getSkusByPage($url, $sort, $page)
    {
        $url = explode('/', $url);
        $url = array_pop($url);
        $url = "{$url}&sort={$sort}&page={$page}";
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->get("https://catalog.wb.ru/sellers/catalog?TestGroup=score_group_21&TestID=388&appType=1&curr=rub&dest=123585811&spp=27&uclusters=6&supplier={$url}");
        return $response->json();
    }

    public static function getCardInfo($nmId)
    {
        $basket = Helper::getBasketNumber($nmId);
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->get("https://basket-{$basket['basket']}.wbbasket.ru/vol{$basket['small']}/part{$basket['mid']}/{$nmId}/info/ru/card.json");
        return $response->json();
    }
}
