<?php


namespace App\Http\Libs;


use Illuminate\Support\Facades\Http;

class WBSupplier
{
    private static function getDetail($nmId): array
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
                //if (!empty(self::$whArrs[$stock['wh']])) {
                    $amount += $stock['qty'];
                //}
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

    private static $whArrs = [
        210284 => 'Белая Дача',
        206236 => 'Белые Столбы',
        210515 => 'Вёшки',
        1733 => 'Екатеринбург - Испытателей 14г',
        300571 => 'Екатеринбург - Перспективный 12/2',
        117986 => 'Казань',
        303295 => 'Клин',
        507 => 'Коледино',
        130744 => 'Краснодар (Тихорецкая)',
        205985 => 'Крыловская',
        302335 => 'Кузнецк',
        208277 => 'Невинномысск',
        686 => 'Новосибирск',
        218210 => 'Обухово',
        312807 => 'Обухово 2',
        117501 => 'Подольск',
        218623 => 'Подольск 3',
        301229 => 'Подольск 4',
        207743 => 'Пушкино',
        300168 => 'Радумля',
        301760 => 'Рязань (Тюшевское)',
        300862 => 'СЦ Абакан 2',
        214951 => 'СЦ Артем',
        209207 => 'СЦ Архангельск',
        302769 => 'СЦ Архангельск (ул Ленина)',
        169872 => 'СЦ Астрахань',
        215020 => 'СЦ Байсерке',
        302737 => 'СЦ Барнаул - Попова',
        210557 => 'СЦ Белогорск',
        216476 => 'СЦ Бишкек',
        217081 => 'СЦ Брянск 2',
        158751 => 'СЦ Владикавказ',
        144649 => 'СЦ Владимир',
        210127 => 'СЦ Внуково',
        301516 => 'СЦ Волгоград Пржевальского',
        300219 => 'СЦ Вологда 2',
        211415 => 'СЦ Воронеж',
        218402 => 'СЦ Иваново',
        218628 => 'СЦ Ижевск',
        131643 => 'СЦ Иркутск',
        117442 => 'СЦ Калуга',
        213849 => 'СЦ Кемерово',
        205205 => 'СЦ Киров',
        140302 => 'СЦ Курск',
        160030 => 'СЦ Липецк',
        209211 => 'СЦ Махачкала',
        117393 => 'СЦ Минск',
        205349 => 'СЦ Мурманск',
        204952 => 'СЦ Набережные Челны',
        118535 => 'СЦ Нижний Новгород',
        211470 => 'СЦ Нижний Тагил',
        206708 => 'СЦ Новокузнецк',
        161520 => 'СЦ Новосибирск Пасечная',
        168458 => 'СЦ Омск',
        206319 => 'СЦ Оренбург',
        218732 => 'СЦ Ош',
        216566 => 'СЦ Пермь 2',
        209209 => 'СЦ Псков',
        301920 => 'СЦ Пятигорск (Этока)',
        218616 => 'СЦ Ростов-на-Дону',
        117230 => 'СЦ Самара',
        158929 => 'СЦ Саратов',
        303189 => 'СЦ Семей',
        169537 => 'СЦ Серов',
        144154 => 'СЦ Симферополь',
        207803 => 'СЦ Смоленск',
        300987 => 'СЦ Смоленск',
        161003 => 'СЦ Сургут',
        209208 => 'СЦ Сыктывкар',
        117866 => 'СЦ Тамбов',
        117456 => 'СЦ Тверь',
        204615 => 'СЦ Томск',
        117819 => 'СЦ Тюмень',
        205104 => 'СЦ Ульяновск',
        300711 => 'СЦ Уральск',
        149445 => 'СЦ Уфа',
        218644 => 'СЦ Хабаровск',
        218225 => 'СЦ Челябинск 2',
        206968 => 'СЦ Чехов',
        218674 => 'СЦ Чита 2',
        218698 => 'СЦ Шымкент',
        207404 => 'СЦ Ярославль',
        2737 => 'Санкт-Петербург (Уткина Заводь)',
        302445 => 'Сынково',
        206348 => 'Тула',
        1193 => 'Хабаровск',
        210001 => 'Чехов 2, Новоселки вл 11 стр 7',
        120762 => 'Электросталь'
    ];
}
