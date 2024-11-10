<?php


namespace App\Http\Libs;


use App\Models\Card;
use App\Models\Seller;
use Exception;
use Illuminate\Support\Facades\Http;

class WBContent
{
    public static function cardsList(Seller $seller, $settings)
    {
        return self::getCards($seller, $settings);
    }

    public static function updateCardDimensions(Seller $seller, Card $card)
    {
        $result = self::getCards($seller, [
            'cursor' => [
                'limit' => 1,
            ],
            'filter' => [
                'textSearch' => (string)$card->nmID,
                "withPhoto" => -1
            ]
        ]);
        if (empty($result['cards'][0])) {
            throw new Exception("card {$card->nmID} not found");
        }
        $cardData = $result['cards'][0];
        $cardData['dimensions']['width'] = (int)$card->dimensions->width;
        $cardData['dimensions']['height'] = (int)$card->dimensions->height;
        $cardData['dimensions']['length'] = (int)$card->dimensions->length;
        $resultUpdate = self::update($seller, $cardData);
        if (!empty($resultUpdate['errorText'])) {
            throw new Exception($resultUpdate['errorText']);
        }
    }

    public static function spp(Seller $seller, $nmId)
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->withCookies([$seller->cookies], 'discounts-prices.wildberries.ru')
            ->post("https://discounts-prices.wildberries.ru/ns/dp-api/discounts-prices/suppliers/api/v1/list/goods/filter", [
                'code' => (string)$nmId,
                'facets' => [],
                'filterWithLeftovers' => false,
                'filterWithoutPrice' => false,
                'limit' => 1,
                'offset' => 0,
                'sortPrice' => true,
                'sortPriceValue' => 0
            ]);
        $response = $response->json();
        if (!empty($response['data']['listGoods'][0]['discountOnSite'])) {
            return $response['data']['listGoods'][0]['discountOnSite'];
        }
        return 0;
    }

    public static function charcs(Seller $seller, int $subjectId): array
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->get("https://suppliers-api.wildberries.ru/content/v2/object/charcs/{$subjectId}?locale=ru");
        return $response->json();
    }

    public static function trash(Seller $seller, array $nmIds)
    {
        $result = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://suppliers-api.wildberries.ru/content/v2/cards/delete/trash", ['nmIDs' => $nmIds]);
        return $result->json();
    }

    public static function uploadPhotos(Seller $seller, array $photos, $nmId)
    {
        $result = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://suppliers-api.wildberries.ru/content/v3/media/save", ['nmId' => $nmId, 'data' => $photos]);
        return $result->json();
    }

    public static function create(Seller $seller, array $card): array|null
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://suppliers-api.wildberries.ru/content/v2/cards/upload", [$card['card']]);
        return $response->json();
    }

    public static function update(Seller $seller, array $card): array|null
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://suppliers-api.wildberries.ru/content/v2/cards/update", [$card]);
        return $response->json();
    }

    public static function objectsAll(Seller $seller, $name)
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withCookies([$seller->cookies], 'seller-content.wildberries.ru')
            ->withToken($seller->apiKey)
            ->post("https://seller-content.wildberries.ru/ns/mapping/content-card/mapping/predict/v3/subjects", [
                'title' => $name
            ]);
        return $response->json();
    }

    public static function objectCharc(Seller $seller, $subjectId)
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->get("https://suppliers-api.wildberries.ru/content/v2/object/charcs/{$subjectId}");
        return $response->json();
    }

    private static function getCards(Seller $seller, $settings)
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://suppliers-api.wildberries.ru/content/v2/get/cards/list", [
                'settings' => $settings
            ]);
        return $response->json();
    }
}
