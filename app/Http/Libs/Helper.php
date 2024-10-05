<?php


namespace App\Http\Libs;


class Helper
{
    public static function randColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    public static function extractSupplerSku($vendorCode)
    {
        $result = explode('-', $vendorCode);
        if (!empty($result[2])) {
            return $result[2];
        }
        return false;
    }

    public static function mbUcfirst($text)
    {
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }

    public static function getBrand($brand)
    {
        if (empty($brand) || $brand == "Сима Ленд" || $brand == "Сималенд" || strtolower($brand) == 'сималенд' || strtolower($brand) == 'сима-ленд' || strtolower($brand) == 'сима ленд') {
            $brand = 'TopGiper';
        }
        return $brand;
    }

    public static function getBasketNumber($nmId)
    {
        $mid = (int)($nmId / 1000);
        $small = (int)($mid / 100);
        if ($small < 144) {
            $basket = '01';
        } elseif ($small < 288) {
            $basket = '02';
        } elseif ($small < 432) {
            $basket = '03';
        } elseif ($small < 720) {
            $basket = '04';
        } elseif ($small < 1008) {
            $basket = '05';
        } elseif ($small < 1062) {
            $basket = '06';
        } elseif ($small < 1116) {
            $basket = '07';
        } elseif ($small < 1170) {
            $basket = '08';
        } elseif ($small < 1314) {
            $basket = '09';
        } elseif ($small < 1602) {
            $basket = '10';
        } elseif ($small < 1656) {
            $basket = '11';
        } elseif ($small < 1920) {
            $basket = '12';
        } elseif ($small < 2046) {
            $basket = '13';
        } elseif ($small < 2190) {
            $basket = '14';
        } elseif ($small < 2406) {
            $basket = '15';
        } else {
            $basket = '16';
        }
        return [
            'basket' => $basket,
            'mid' => $mid,
            'small' => $small
        ];
    }

    public static function arrSearch($array, $key, $value)
    {
        $results = "";
        self::arrSearchR($array, $key, $value, $results);
        return $results;
    }

    public static function arrSearchR($array, $key, $value, &$results)
    {
        if (!is_array($array)) {
            return;
        }

        if (isset($array[$key]) && $array[$key] == $value) {
            $results = $array['value'];
        }

        foreach ($array as $subarray) {
            self::arrSearchR($subarray, $key, $value, $results);
        }
    }
}
