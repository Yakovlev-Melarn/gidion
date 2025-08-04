<?php


namespace App\Http\Libs;


use App\Jobs\UploadImages;
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
            ->get("https://content-api.wildberries.ru/content/v2/object/charcs/{$subjectId}?locale=ru");
        return $response->json();
    }

    public static function trash(Seller $seller, array $nmIds)
    {
        $result = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://content-api.wildberries.ru/content/v2/cards/delete/trash", ['nmIDs' => $nmIds]);
        return $result->json();
    }

    public static function uploadPhotos(Seller $seller, array $photos, $nmId)
    {
        $result = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://content-api.wildberries.ru/content/v3/media/save", ['nmId' => $nmId, 'data' => $photos]);
        print_r(['nmId' => $nmId, 'data' => $photos]);
        $resultData = $result->json();
        if (!empty($resultData['errorText'])) {
            if (!empty($photos)) {
                array_pop($photos);
                $card = Card::where('nmID', '=', $nmId)->where("seller_id", '=', $seller->id)->first();
                UploadImages::dispatch($seller, $photos, $card['vendorCode'])->onQueue('uploadphotos');
            }
        }
        return $result->json();
    }

    public static function create(Seller $seller, array $card): array|null
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://content-api.wildberries.ru/content/v2/cards/upload", [$card['card']]);
        return $response->json();
    }

    public static function update(Seller $seller, array $card): array|null
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://content-api.wildberries.ru/content/v2/cards/update", [$card]);
        return $response->json();
    }

    public static function objectsAll(Seller $seller, $name)
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->get("https://content-api.wildberries.ru/content/v2/object/all", [
                'locale' => 'ru',
                'name' => $name
            ]);
        return $response->json();
    }
    public static function objectsAll2(Seller $seller, $name)
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->withHeader('Authorizev3','eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NTE1NjY3NzAsInVzZXIiOiIxMDA1ODU5NjIiLCJzaGFyZF9rZXkiOiIxNiIsImNsaWVudF9pZCI6InNlbGxlci1wb3J0YWwiLCJzZXNzaW9uX2lkIjoiOTQyZDE0OTZjOWNiNGNlZDliYjcxMGFmZmQzYTFjM2QiLCJ2YWxpZGF0aW9uX2tleSI6IjU2YzU3NGUxNTZmYmVhZjEzNmYxMjRlMDIzNzEwZWQxZTcwNWE5NTI3MjM0OWE2OTE4ODIwMTE1ZWIzZTUxMGYiLCJ1c2VyX3JlZ2lzdHJhdGlvbl9kdCI6MTY5MzE0MjI3NSwidmVyc2lvbiI6Mn0.DjSF4JmoLqqCQ7w9mxdUYxQIT1w4Qr5A-lb3I8jumZJd4sYCbp0cOpEZjUsCaFDJZ3UVcNCIFGFVEhc7N9zcx6DYmZGrVWzOzWZ-kdUz-Ihd5Ta1vQDxVY-4MHnRdpoYAYhDA5WZBrH_FLX4tIC9lLdVctYUzRm7IKzWI0-kJl78CNHhpBOwGUz8Wbs6WcLpIw_cIpVCFBVRDD2TetkMBSeT8lI61mi4-ABGXWcBKk2tn-FdP1BCZNBpY4GVWEy6L_UL2FLzX4sBSlaNLfqSLFYckKPDgvxmCCSXQfMr_WQwiSjyROe4kRZ5_pNemslW1678O7g9Pc2XAMh2leewog')
            ->withCookies([$seller->cookies], 'seller-content.wildberries.ru')
            ->post("https://seller-content.wildberries.ru/ns/mapping/content-card/mapping/predict/v6/subjects", [
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
            ->get("https://content-api.wildberries.ru/content/v2/object/charcs/{$subjectId}");
        return $response->json();
    }

    private static function getCards(Seller $seller, $settings)
    {
        $response = Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->withToken($seller->apiKey)
            ->post("https://content-api.wildberries.ru/content/v2/get/cards/list", [
                'settings' => $settings
            ]);
        return $response->json();
    }
}
