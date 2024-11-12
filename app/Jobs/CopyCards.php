<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\Helper;
use App\Http\Libs\WBContent;
use App\Http\Libs\WBSupplier;
use App\Models\Card;
use App\Models\CopyCard;
use App\Models\Seller;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CopyCards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    public Seller $seller;
    public Supplier $supplier;
    public string $competitor;
    public int $count;
    public int $catalog = 5;
    public array $sortTypes = [
        'newly',
        'rate',
        'priceup',
        'benefit',
        'popular',
        'pricedown'
    ];

    public function __construct(Seller $seller, $competitor, $count)
    {
        $this->seller = $seller;
        $this->competitor = $competitor;
        $this->count = (int)$count;
        $this->supplier = Supplier::where('name', 'Wildberries')->first();
    }

    public function handle(): void
    {
        echo "Предварительно карточек товара будет создано: {$this->count}. \r\n";
        Helper::writeLog("CopyCardsInfo", "Будет создано {$this->count} карточек товара.");
        foreach ($this->sortTypes as $sortType) {
            for ($i = 1; $i <= 100; $i++) {
                if (CopyCard::count() >= $this->count) {
                    break;
                }
                $this->prepareCopyCard($sortType, $i);
            }
        }
        $this->copyCard();
        $settings = [
            'cursor' => [
                'limit' => 100
            ],
            'filter' => [
                "withPhoto" => -1
            ]
        ];
        SyncCardsJob::dispatch($this->seller, $settings);
    }

    private function getPrefixSku(): string
    {
        $prefixSku = 'RS-X';
        if ($this->competitor == 'https://www.wildberries.ru/seller/998550') {
            $prefixSku = 'CT-X';
        }
        return $prefixSku;
    }

    private function getCopyProducts($supplierSkus): bool|array
    {
        if (empty($supplierSkus['data']['products'])) {
            return false;
        }
        return $supplierSkus['data']['products'];
    }

    private function validateSupplierSku($supplierSku, $prefixSku): bool
    {
        if (Card::where("vendorCode", "{$prefixSku}-{$supplierSku['id']}-1")->first()) {
            Helper::writeLog("CopyCardsInfo", "Карточка nmId {$supplierSku['id']} уже создана.", 'issetCopiedCard');
            return false;
        }
        if ($supplierSku['totalQuantity'] < 5) {
            Helper::writeLog("CopyCardsInfo", "Карточка nmId {$supplierSku['id']} без остатка.", 'zeroQuantityCopiedCard');
            return false;
        }
        $price = $supplierSku['salePriceU'] / 100;
        if ($price > 300) {
            Helper::writeLog("CopyCardsInfo", "Карточка nmId {$supplierSku['id']} стоит {$price} руб., это выше допустимого порога.", 'highPriceCopiedCard');
            return false;
        }
        if (empty($supplierSku['name'])) {
            Helper::writeLog("CopyCardsInfo", "Карточка nmId {$supplierSku['id']} без названия.", 'emptyNameCopiedCard');
            return false;
        }
        return true;
    }

    private function setCopyCards($copySkus, $prefixSku)
    {
        foreach ($copySkus as $supplierSku) {
            if ($this->validateSupplierSku($supplierSku, $prefixSku)) {
                if (CopyCard::count() >= $this->count) {
                    break;
                }
                $this->addQueueCopyCards($supplierSku, $prefixSku);
            }
        }
    }

    private function addQueueCopyCards($supplierSku, $prefixSku)
    {
        if (!CopyCard::find($supplierSku['id'])) {
            $price = $supplierSku['salePriceU'] * 5 / 100;
            $copyCard = new CopyCard();
            $copyCard->id = $supplierSku['id'];
            $copyCard->price = $price;
            $copyCard->name = $supplierSku['name'];
            $copyCard->subjectId = $supplierSku['subjectId'];
            $copyCard->quantity = $supplierSku['totalQuantity'];
            $copyCard->prefix = $prefixSku;
            $copyCard->save();
        }
    }

    private function sendCopyCards($copyCards)
    {
        foreach ($copyCards as $copyCard) {
            $data = WBSupplier::getCardInfo($copyCard->id);
            $data['imt_name'] = $copyCard->name;
            if ($cardData = self::fillCardData(
                $data, $copyCard->subjectId, $this->seller, $copyCard->prefix, 1, $copyCard->price
            )) {
                $result = WBContent::create($this->seller, $cardData);
                if (!empty($result['error'])) {
                    Helper::writeLog("CopyCardsInfo", $result['errorText'], 'errorCopiedCard');
                }
                $cardData['vendorCode'] = "RS-X-{$copyCard->id}-1";
                CardLib::makeJobAfterCreateCard($this->seller, $cardData);
                $copyCard->delete();
            }
        }
    }

    private function copyCard()
    {
        $copyCards = CopyCard::all();
        echo "Обработка окочена. Карточек в очереди на создание: {$copyCards->count()}\r\n";
        Helper::writeLog("CopyCardsInfo", "Обработка окочена. Карточек в очереди на создание: {$copyCards->count()}");
        if ($copyCards->count() > 0) {
            $this->sendCopyCards($copyCards);
        }
    }

    private function prepareCopyCard($sort, $page)
    {
        $supplierSkus = WBSupplier::getSkusByPage($this->competitor, $sort, $page);
        if ($copySkus = $this->getCopyProducts($supplierSkus)) {
            $prefixSku = $this->getPrefixSku();
            $this->setCopyCards($copySkus, $prefixSku);
        }
    }

    public static function fillCardData($data, $subjectId, $seller, $prefix = 'RS-X', $pack = 1, $price = null): array|bool
    {
        if (!empty($data['description'])) {
            $data['description'] = mb_substr($data['description'], 0, 1999);
        }
        $data['sellPrice'] = WBSupplier::getPrice($data['nm_id']);
        $photos = CardLib::getPhotosBySupplierProduct($data['nm_id'], $data['media']['photo_count']);
        if (!empty($photos)) {
            $chrs = WBContent::charcs($seller, $subjectId);
            if (!empty($chrs['data'])) {
                $dimensions = ['width' => 10, 'length' => 10, 'height' => 10];
                $characteristics = [];
                if (!empty($data['options'])) {
                    foreach ($data['options'] as &$option) {
                        foreach ($chrs['data'] as $chr) {
                            if ($chr['name'] == $option['name']) {
                                $option['charcID'] = $chr['charcID'];
                                $option['maxCount'] = $chr['maxCount'];
                            }
                        }
                        if (!empty($option['charcID'])) {
                            if ($option['charcID'] == 90849) {
                                $dimensions['width'] = (int)$option['value'];
                            } elseif ($option['charcID'] == 90846) {
                                $dimensions['length'] = (int)$option['value'];
                            } elseif ($option['charcID'] == 90745) {
                                $dimensions['height'] = (int)$option['value'];
                            } else {
                                $characteristics[] = [
                                    'id' => $option['charcID'],
                                    'value' => $option['value'],
                                    'maxcount' => $option['maxCount']
                                ];
                            }
                        }
                    }
                }
                $characteristicsList = [];
                foreach ($characteristics as $characteristic) {
                    $values = explode("; ", $characteristic['value']);
                    if ($characteristic['id'] == 18182) {
                        $values = $characteristic['value'];
                    } else {
                        if ($characteristic['maxcount'] == 0 && count($values) == 1) {
                            $values = (int)$characteristic['value'];
                        }
                        if ($characteristic['maxcount'] == 1 && count($values) > 1) {
                            $values = [$values[0]];
                        }
                        if ($characteristic['id'] == 14177449) {
                            foreach ($values as &$value) {
                                $value = str_replace('Чёрный', 'черный', $value);
                                if ($value == 1) {
                                    unset($value);
                                }
                            }
                        }
                    }
                    $characteristicsList[] = [
                        'id' => (int)$characteristic['id'],
                        'value' => $values
                    ];
                }
                if (empty($price)) {
                    $price = (int)ceil((($data['sellPrice'] + 55) / (100 - $seller->percentageOfMargin)) * 100);
                }
                if (empty($data['selling']['brand_name'])) {
                    $data['selling']['brand_name'] = 'TopGiper';
                }
                $result = [
                    'card' => [
                        "subjectID" => (int)$subjectId,
                        "variants" => [
                            [
                                "vendorCode" => "{$prefix}-{$data['nm_id']}-{$pack}",
                                "title" => Helper::mbUcfirst(mb_substr(mb_strtolower($data['imt_name']), 0, 59)),
                                "description" => empty($data['description']) ? $data['imt_name'] : $data['description'],
                                "brand" => Helper::getBrand($data['selling']['brand_name']),
                                "dimensions" => [
                                    "height" => (int)$dimensions['height'],
                                    "length" => (int)$dimensions['length'],
                                    "width" => (int)$dimensions['width']
                                ],
                                "characteristics" => $characteristicsList,
                                'sizes' => [[
                                    "price" => (int)$price
                                ]]
                            ]
                        ]
                    ],
                    'photos' => $photos
                ];
                return $result;
            }
        }
        return false;
    }
}
