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
use App\Models\SupplierCaregory;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CopyCards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $uniqueFor = 360000;
    public $timeout = 360000;
    public Seller $seller;
    public Supplier $supplier;
    public string $competitor;
    public int $count;
    public int $catalog = 5;
    public int $supplierId;
    public array $categories = [];
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
        $this->supplierId = Helper::getSupplierID($competitor);
    }

    public function handle(): void
    {
        $this->getSupplierCategories();
        echo "Предварительно карточек товара будет создано: {$this->count}. \r\n";
        Helper::writeLog("CopyCardsInfo", "Будет создано {$this->count} карточек товара.");
        $categories = SupplierCaregory::where("supplierId", $this->supplierId)->orderBy('categoryId')->get();
        foreach ($categories as $category) {
            if (!$category->checked) {
                if ($category->productsCount > 0) {
                    echo "Опрашиваю категорию: {$category->name}. \r\n";
                    if (!$this->count) {
                        break;
                    }
                    echo "Осталось создать карточек:  {$this->count}\r\n";
                    echo "Опрашиваю категорию: {$category->name}. Товаров: {$category->productsCount} \r\n";
                    $pages = ceil($category->productsCount / 100);
                    for ($i = 1; $i <= $pages; $i++) {
                        $this->prepareCopyCard($category->categoryId, $i);
                    }
                    $this->copyCard();
                }
                $category->checked = 1;
                $category->save();
            }
        }
        /*        $settings = [
                    'cursor' => [
                        'limit' => 100
                    ],
                    'filter' => [
                        "withPhoto" => -1
                    ]
                ];*/
        //SyncCardsJob::dispatch($this->seller, $settings);
    }

    private function getSupplierCategories()
    {
        SupplierCaregory::where("supplierId", $this->supplierId)->where('created_at', '<', date('Y-m-d'))->delete();
        $categories = WBSupplier::getCategoriesBySupplier($this->supplierId);
        try {
            $items = $categories['data']['filters'][0]['items'];
            foreach ($items as $category) {
                if (!SupplierCaregory::where('categoryId', $category['id'])->first()) {
                    $supplierCategory = new SupplierCaregory();
                    $supplierCategory->categoryId = $category['id'];
                    $supplierCategory->supplierId = $this->supplierId;
                    $supplierCategory->productsCount = $category['count'];
                    $supplierCategory->name = $category['name'];
                    $supplierCategory->save();
                }
            }
        } catch (Exception $e) {
            Helper::writeLog("CopyCardsInfo", "Не удалось получить дерево категорий. {$e->getMessage()}", 'emptyCategories');
        }
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
            if (empty($data)) {
                dd($copyCard);
            }
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
                $this->count--;
            }
        }
    }

    private function copyCard()
    {
        $copyCards = CopyCard::where("price", '>', '0')->limit($this->count)->get();
        echo "Обработка окочена. Карточек в очереди на создание: {$copyCards->count()}\r\n";
        Helper::writeLog("CopyCardsInfo", "Обработка окочена. Карточек в очереди на создание: {$copyCards->count()}");
        if ($copyCards->count() > 0) {
            $this->sendCopyCards($copyCards);
        }
    }

    private function prepareCopyCard($categoryId, $page)
    {
        $supplierSkus = WBSupplier::getSkusByPage($this->competitor, $categoryId, $page);
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
        if (isset($data['media']['photo_count'])) {
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
                        } elseif ($characteristic['id'] == 19033) {
                            $values = [$values[0]];
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
        }
        return false;
    }
}
