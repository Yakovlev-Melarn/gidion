<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\Helper;
use App\Http\Libs\WBContent;
use App\Http\Libs\WBSupplier;
use App\Models\Card;
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

    public $tries = 50;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    public Seller $seller;
    public Supplier $supplier;
    public string $competitor;
    public int $count;
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
        $this->supplier = Supplier::find(5);
        if (WBContent::limits($seller) < (int)$count) {
            $this->count = WBContent::limits($seller);
        }
    }

    public function handle(): void
    {
        $added = 0;
        $startPage = $this->supplier->skipPage + 1;
        foreach ($this->sortTypes as $sortType) {
            for ($i = $startPage; $i < 100; $i++) {
                if ($this->count == $added) {
                    break;
                }
                if ($cardData = $this->copyCard($sortType, $i)) {
                    CardLib::makeJobAfterCreateCard($this->seller, $cardData);
                    $added++;
                }
            }
        }
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

    private function copyCard($sort, $page): bool|array
    {
        $supplierSkus = WBSupplier::getSkusByPage($this->competitor, $sort, $page);
        /**
         * @todo переписать примерно как-то так..
         * $this->copySkus($supplierSkus);
         *      copySkus($supplierSkus)
         *          if($this->>validateDataProducts($supplierSkus)){
         *              $supplierSkus = $this->getSkusByPage($supplierSkus);
         *              $skiped = 0;
         * $prefixSku = $this->>getPrefixSku();
         *          }
         *      getSkusByPage
         *          return $supplierSkus['data']['products'];
         *      validateDataProducts($supplierSkus)
         *          if(empty($supplierSkus['data']['products'])
         *              return false;
         *          if(Card::where("vendorCode", "{$prefixSku}-{$supplierSku['id']}-1")->first())
         *              return false;
         *          if (!WBSupplier::getAmount($supplierSku['id']))
         *              return false;
         *          if ($sellPrice > 300)
         *              return false;
         *          if (empty($data['imt_name']))
         *              return false;
         * .......
         */
        if (!empty($supplierSkus['data']['products'])) {
            $skiped = 0;
            $prefixSku = 'RS-X';
            if ($this->competitor == 'https://www.wildberries.ru/seller/998550') {
                $prefixSku = 'CT-X';
            }
            foreach ($supplierSkus['data']['products'] as $supplierSku) {
                if (Card::where("vendorCode", "{$prefixSku}-{$supplierSku['id']}-1")->first()) {
                    $skiped++;
                    if ($skiped == 100) {
                        $this->supplier->skipPage += 1;
                        $this->supplier->save();
                    }
                    echo "{$supplierSku['id']} уже создано ";
                    continue;
                }
                if (!WBSupplier::getAmount($supplierSku['id'])) {
                    $skiped++;
                    if ($skiped == 100) {
                        $this->supplier->skipPage += 1;
                        $this->supplier->save();
                    }
                    echo "{$supplierSku['id']} нет в наличии ";
                    continue;
                }
                $data = WBSupplier::getCardInfo($supplierSku['id']);
                if (!empty($data['nm_id'])) {
                    $sellPrice = WBSupplier::getPrice($data['nm_id']);
                    if ($sellPrice > 300) {
                        $skiped++;
                        if ($skiped == 100) {
                            $this->supplier->skipPage += 1;
                            $this->supplier->save();
                        }
                        echo "Товар стоит {$sellPrice} руб., это выше допустимого порога.";
                        continue;
                    }
                } else {
                    continue;
                }
                if (!empty($data['imt_name'])) {
                    if ($cardData = self::fillCardData($data, $supplierSku['subjectId'], $this->seller, $prefixSku, 1, $sellPrice * 5)) {
                        $result = WBContent::create($this->seller, $cardData);
                        if (!empty($result['error'])) {
                            return false;
                        }
                        $cardData['vendorCode'] = "RS-X-{$supplierSku['id']}-1";
                        return $cardData;
                    }
                }
            }
        }
        return false;
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
        return false;
    }
}
