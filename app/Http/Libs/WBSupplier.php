<?php


namespace App\Http\Libs;


use App\Http\Libs\Telegramm;
use App\Models\Card;
use Illuminate\Support\Facades\Http;

class WBSupplier
{
    public static function getDetail($nmId): array | null
    {
        $result = Http::withHeaders([])
            ->timeout(180)
            ->connectTimeout(180)
            ->acceptJson()
            ->get("https://card.wb.ru/cards/v4/detail?appType=1&curr=rub&dest=-4438503&spp=30&hide_dtype=14&ab_testing=false&lang=ru&nm={$nmId}");
        return $result->json();
    }

    public static function getAmounts(string $nmIds): array|bool
    {
        $result = false;
        $response = self::getDetail($nmIds);
        if (isset($response['products'])) {
            foreach ($response['products'] as $productDetail) {
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

    public static function getPrices(string $nmId, $seller): array|false
    {
        $response = self::getDetail($nmId);
        $result = false;
        if (!empty($response['data']['products'])) {
            foreach ($response['data']['products'] as $product) {
                if (!isset($product['salePriceU'])) {
                    if ($card = Card::where('supplierSku', $nmId)->first()) {
                        if ($card->prices) {
                            if (!empty($card->prices->s_price)) {
                                $product['salePriceU'] = $card->prices->s_price * 100;
                            }
                        }
                    }
                    if (!isset($product['salePriceU'])) {
                        Telegramm::send("Нет цены закупки для товара {$nmId}. Продавец {$seller->name}\r\n", $seller->user->id);
                        continue;
                    }
                }
                $result[$product['id']] = $product['salePriceU'] / 100;
            }
            return $result;
        }
        return false;
    }

    public static function getPrice(int $nmId): int|bool
    {
        $response = self::getDetail($nmId);
        if(isset($response['products'][0]['sizes'][0]['price'])) {
            if (!empty($price = $response['products'][0]['sizes'][0]['price']['product'])) {
                return (int)$price / 100;
            }
        }
        return false;
    }

    public static function getCategoriesBySupplier($supplierId)
    {
        $response = Http::withHeaders([])
            ->timeout(180)
            ->connectTimeout(180)
            ->acceptJson()
            ->get("https://catalog.wb.ru/sellers/v8/filters?ab_testing=false&appType=1&curr=rub&dest=-1412209&filters=xsubject&spp=30&supplier={$supplierId}&uclusters=8");
        return $response->json();
    }

    public static function getSkusByPage($url, $categoryId, $page)
    {
        $supplierId = Helper::getSupplierID($url);
        $url = "{$supplierId}&xsubject={$categoryId}&page={$page}";
        $response = Http::withHeaders([])
            ->timeout(180)
            ->connectTimeout(180)
            ->acceptJson()
            ->get("https://catalog.wb.ru/sellers/v2/catalog?TestGroup=score_group_21&TestID=388&appType=1&curr=rub&dest=123585811&spp=27&uclusters=6&supplier={$url}");
        return $response->json();
    }

    public static function getCardInfo($nmId)
    {
        $basket = Helper::getBasketNumber($nmId);
        $response = Http::withHeaders([])
            ->timeout(180)
            ->connectTimeout(180)
            ->acceptJson()
            ->get("https://basket-{$basket['basket']}.wbbasket.ru/vol{$basket['small']}/part{$basket['mid']}/{$nmId}/info/ru/card.json");
        return $response->json();
    }
}
