<?php

namespace App\Http\Libs;

use Illuminate\Support\Facades\Http;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

class OfficeBurg
{
    public static function getPrice($sku)
    {
        $url = "https://office-burg.ru/search/?q={$sku}";
        $response = Http::withHeaders([])->withCookies(['_ym_uid=1714106427158212690; _ym_d=1714106427; BX_USER_ID=5d5f4fcb0972ba605ed2af43604616a2; OP_SAMSON_OP_DEALER_UUID=130d3443-68e0-53b5-9a72-98e83e256e89; OP_SAMSON_OP_DEALER_ID=9087558; OP_SAMSON_CITY_UUID=4e609969-e8e0-513c-a4c0-17cd878c4a65; OP_SAMSON_SALE_UID=1161289077; OP_SAMSON_LOGIN=melarn4u%40gmail.com; OP_SAMSON_SOUND_LOGIN_PLAYED=Y; warning=true; list_sort=PRICE_ASC; PHPSESSID=r51dp9n2nll2iir2nm71f8t28j; OP_SAMSON_WATCHED_GOODS=a%3A16%3A%7Bi%3A1714195240%3Bs%3A6%3A%22270827%22%3Bi%3A1714543912%3Bs%3A6%3A%22291294%22%3Bi%3A1714195317%3Bs%3A6%3A%22361175%22%3Bi%3A1715161658%3Bs%3A6%3A%22403793%22%3Bi%3A1714195357%3Bs%3A6%3A%22404624%22%3Bi%3A1714131142%3Bs%3A6%3A%22510117%22%3Bi%3A1714131179%3Bs%3A6%3A%22605392%22%3Bi%3A1714558662%3Bs%3A6%3A%22610878%22%3Bi%3A1715080679%3Bs%3A6%3A%22620009%22%3Bi%3A1715082277%3Bs%3A6%3A%22631024%22%3Bi%3A1715160614%3Bs%3A6%3A%22662229%22%3Bi%3A1715083349%3Bs%3A6%3A%22664681%22%3Bi%3A1714558497%3Bs%3A6%3A%22665312%22%3Bi%3A1714558494%3Bs%3A6%3A%22665313%22%3Bi%3A1714558485%3Bs%3A6%3A%22665314%22%3Bi%3A1715406776%3Bs%3A6%3A%22440112%22%3B%7D; _gid=GA1.2.1581836468.1715406787; _ym_isad=2; WRAP_GA_COUNT=NaN; OP_SAMSON_BANNERS=0_17576_3_18052024%2C0_15810_1_18052024%2C0_17609_1_18052024%2C0_17567_1_18052024%2C0_17410_1_18052024%2C0_17673_1_18052024%2C0_17386_1_18052024%2C0_16728_1_18052024; _ga=GA1.1.1695913439.1714106427; _ym_visorc=w; _gat=1; _ga_SRSW4MH0TF=GS1.1.1715413208.19.1.1715413231.0.0.0'], 'office-burg.ru')->get($url);
        $body = $response->body();
        $body = iconv('windows-1251', 'utf-8', $body);
        $dom = new Dom();
        $options = new Options();
        $dom->loadStr($body, $options->setEnforceEncoding('UTF-8'));
        $contents = $dom->find('.Product__buy');
        $dom->loadStr($contents);
        $contents = $dom->find('.Price');
        if(count($contents) > 0) {
            return floatval($contents->getAttribute('data-base-price'));
        }
        return 0;
    }
}
