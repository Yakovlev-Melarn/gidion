<?php


namespace App\Http\Libs;


use App\Jobs\CalcPrice;
use App\Jobs\ContentCard;
use App\Jobs\ContentCardDimensions;
use App\Jobs\DiscountRemove;
use App\Jobs\DiscountUpdate;
use App\Jobs\ProductStockUpdate;
use App\Jobs\StockUpdate;
use App\Jobs\UploadImages;
use App\Models\Card;
use App\Models\CardCharacteristics;
use App\Models\CardDimensions;
use App\Models\CardPhoto;
use App\Models\CardSizes;
use App\Models\Catalog;
use App\Models\Seller;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Exception as Exc;

class CardLib
{
    public static float $costOfLogistics = 55;
    public static float $liter = 7;
    public static float $tariff = 4.5;

    public static function getDeliveryPrice($card)
    {
        $volumetricWeight = ($card->dimensions->width * $card->dimensions->height * $card->dimensions->length) / 1000;
        $costOfLogistics = self::$costOfLogistics;
        if ($volumetricWeight > 1) {
            $costOfLogistics = self::$costOfLogistics + (($volumetricWeight - 1) * self::$liter);
        }
        return $costOfLogistics;
    }

    public static function getComission($card)
    {
        if (!$card->comission) {
            throw new Exc("Нет комиссии для товара категории {$card->subjectName}");
        }
        return $card->comission->comission + self::$tariff;
    }

    public static function updateDimensions(Card $card, Request $request, Seller $seller, $upload = true)
    {
        $card->dimensions->width = $request->cardDimensionsWidth;
        $card->dimensions->height = $request->cardDimensionsHeight;
        $card->dimensions->length = $request->cardDimensionsLength;
        $card->dimensions->save();
        if ($upload) {
            ContentCardDimensions::dispatch($seller, $card);
        }
    }

    public static function update(Card $card, Request $request, Seller $seller, $uploadStocks = true)
    {
        self::updateDimensions($card, $request, $seller);
        if ($request->supplierPrice != $card->prices->s_price) {
            $card->prices->s_price = $request->supplierPrice;
            $card->prices->percent = 1;
            $card->prices->toUpload = 1;
            $card->prices->save();
            CalcPrice::dispatch($seller, $seller->percentageOfMargin, $card->id);
        }
        if ($request->sellPrice != $card->prices->price) {
            $card->prices->price = $request->sellPrice;
            $card->prices->toUpload = 1;
            $card->prices->save();
        }
        if ($request->discount != $card->prices->discount) {
            $card->prices->discount = $request->discount;
            $card->prices->save();
            if ($card->prices->discount == 0) {
                DiscountRemove::dispatch($seller, $card);
            } else {
                DiscountUpdate::dispatch($seller, $card);
            }
        }
        if (empty($card->slstock)) {
            self::createEmptyStock($card->id, $seller->id);
            $card = Card::find($card->id);
        }
        if ($request->localAmount == 0 && $card->slstock->is_local == 1) {
            $card->slstock->is_local = 0;
            $card->slstock->amount = 0;
            $card->slstock->address = 'supplier';
            $card->slstock->save();
            if ($card->supplier == 10) {
                $card->removeByStock = 0;
                $card->save();
                $card->prices->percent = 2;
                $card->prices->save();
                CalcPrice::dispatch($seller, $seller->percentageOfMargin, $card->id);
                StockUpdate::dispatch($seller);
            } else {
                if ($uploadStocks) {
                    foreach ($card->sizes as $size) {
                        ProductStockUpdate::dispatch($seller, 0, $size->skus);
                        break;
                    }
                }
            }
        }
        if ($request->localAmount > 0) {
            $card->removeByStock = 1;
            $card->slstock->is_local = 1;
            $card->slstock->amount = $request->localAmount;
            $card->slstock->address = $request->address;
            $card->slstock->save();
            if ($uploadStocks) {
                foreach ($card->sizes as $size) {
                    ProductStockUpdate::dispatch($seller, $card->slstock->amount, $size->skus);
                    break;
                }
            }
        }
        if (($request->removeStock == 1) && ($card->removeByStock == 0)) {
            $card->removeByStock = $request->removeStock;
            $card->save();
            if ($uploadStocks) {
                foreach ($card->sizes as $size) {
                    ProductStockUpdate::dispatch($seller, 0, $size->skus);
                }
            }
        }
    }

    public static function createEmptyStock($cardId, $sellerId)
    {
        $stock = new Stock();
        $stock->card_id = $cardId;
        $stock->seller_id = $sellerId;
        $stock->save();
        return $stock;
    }

    public static function getLocalStock($id, $sellerId)
    {
        if (!$stock = Stock::where("card_id", $id)->first()) {
            $stock = self::createEmptyStock($id, $sellerId);
        }
        return [
            'amount' => $stock->is_local ? $stock->amount : 0,
            'address' => $stock->address
        ];
    }

    public static function getSellStockPrice($id)
    {
        $card = Card::find($id);
        if (empty($card->prices)) {
            return [
                'price' => false
            ];
        }
        if (!$card->prices->s_price) {
            $card->prices->s_price = WBSupplier::getPrice($card->supplierSku);
            $card->prices->save();
        }
        $deliveryPrice = self::getDeliveryPrice($card);
        $comissionPercent = self::getComission($card) / 100;
        $comission = ceil($card->prices->price * $comissionPercent);
        $profit = $card->prices->price - $comission - $deliveryPrice - $card->prices->s_price;
        $price = ceil($card->prices->price - ($profit / (100 - ($comissionPercent * 100))) * 100);
        return [
            'status' => 1,
            'discount' => 0,
            'price' => $price
        ];
    }

    public static function promiseSupplier(Card $card): Card
    {
        if (empty($card->supplier)) {
            $prefix = substr($card->vendorCode, 0, 1);
            if ($supplier = Supplier::where('user_id', $card->seller->user_id)->where("prefix", $prefix)->first()) {
                $card->supplier = $supplier->supplierId;
                $card->save();
            }
        }
        return $card;
    }

    public static function promiseSku(Card $card): Card
    {
        if (empty($card->supplierSku)) {
            $segments = explode('-', $card->vendorCode);
            if ($card->supplier == 11) {
                $card->supplierSku = $segments[1];
                $card->save();
                if ($product = Catalog::where('sku', $card->supplierSku)->first()) {
                    $product->card_id = $card->id;
                }
            } else {
                if (count($segments) > 2) {
                    $card->supplierSku = $segments[2];
                    $card->save();
                }
            }
        }
        return $card;
    }

    public static function addPhoto(array $cardData)
    {
        $card = Card::where("nmID", $cardData['nmID'])->first();
        self::addPhotos($card->id, $cardData['photos']);
    }

    public static function addSize(array $cardData)
    {
        $card = Card::where("nmID", $cardData['nmID'])->first();
        self::addSizes($card->id, $cardData['photos'], $card->seller_id);
    }

    public static function addCard(array $cardData, $seller): Card
    {
        $savePrice = false;
        if ($card = Card::where("nmID", $cardData['nmID'])->where("seller_id", $seller->id)->first()) {
            $savePrice = true;
            $id = $card->id;
            if ($card->prices) {
                $prices = $card->prices;
            }
            $card->delete();
            $card = new Card();
            $card->id = $id;
        } else {
            $card = new Card();
        }
        $card->nmID = $cardData['nmID'];
        $card->imtID = $cardData['imtID'];
        $card->subjectID = $cardData['subjectID'];
        $card->vendorCode = $cardData['vendorCode'];
        $card->subjectName = $cardData['subjectName'];
        $card->brand = $cardData['brand'];
        $card->title = $cardData['title'];
        $card->createdAt = date("Y-m-d H:i:s", strtotime($cardData['createdAt']));
        $card->updatedAt = date("Y-m-d H:i:s", strtotime($cardData['updatedAt']));
        $card->seller_id = $seller->id;
        $card->save();
        if ($savePrice && $card->prices) {
            $card->prices = $prices;
            $card->prices->save();
        }
        $card = self::promiseSupplier($card);
        $card = self::promiseSku($card);
        if (!empty($cardData['photos'])) {
            self::addPhotos($card->id, $cardData['photos']);
        }
        self::addDimensions($card->id, $cardData['dimensions']);
        if (isset($cardData['characteristics'])) {
            self::addCharacteristics($card->id, $cardData['characteristics']);
        }
        self::addSizes($card->id, $cardData['sizes'], $seller->id);
        return $card;
    }

    public static function addPhotos(int $id, array $photos): void
    {
        foreach ($photos as $photo) {
            $cardPhotos = new CardPhoto();
            $cardPhotos->card_id = $id;
            $cardPhotos->big = $photo['big'];
            $cardPhotos->save();
        }
    }

    public static function addDimensions(int $id, array $dimensions): void
    {
        $cardDimensions = new CardDimensions();
        $cardDimensions->card_id = $id;
        $cardDimensions->length = $dimensions['length'];
        $cardDimensions->width = $dimensions['width'];
        $cardDimensions->height = $dimensions['height'];
        $cardDimensions->save();
    }

    public static function addCharacteristics(int $id, array $characteristics): void
    {
        foreach ($characteristics as $characteristic) {
            $cardCharacteristic = new CardCharacteristics();
            $cardCharacteristic->card_id = $id;
            $cardCharacteristic->cid = $characteristic['id'];
            $cardCharacteristic->name = $characteristic['name'];
            if (is_array($characteristic['value'])) {
                $cardCharacteristic->value = implode(';', $characteristic['value']);
            } else {
                $cardCharacteristic->value = $characteristic['value'];
            }
            $cardCharacteristic->save();
        }
    }

    public static function addSizes(int $id, array $sizes, int $sellerId): void
    {
        foreach ($sizes as $size) {
            $cardSize = new CardSizes();
            $cardSize->card_id = $id;
            $cardSize->seller_id = $sellerId;
            $cardSize->chrtID = $size['chrtID'];
            $cardSize->techSize = $size['techSize'];
            $cardSize->wbSize = $size['wbSize'];
            $cardSize->skus = $size['skus'][0];
            $cardSize->save();
        }
    }

    public static function getProductsWithoutPhotos($seller)
    {
        $result = WBContent::cardsList($seller, [
            'cursor' => [
                'limit' => 100
            ],
            'filter' => [
                "withPhoto" => 0
            ]
        ]);
        return new Data($result['cards']);
    }

    public static function getPhotosBySupplierProduct($nmId, $photoCount)
    {
        $basket = Helper::getBasketNumber($nmId);
        $imagesUrl = "https://basket-{$basket['basket']}.wbbasket.ru/vol{$basket['small']}/part{$basket['mid']}/{$nmId}/images/big/";
        $photos = [];
        for ($i = 1; $i <= $photoCount; $i++) {
            $photos[] = "{$imagesUrl}{$i}.webp";
        }
        return $photos;
    }

    public static function makeJobAfterCreateCard($seller, $cardData)
    {
        Bus::chain([
            new ContentCard($seller, [
                'cursor' => [
                    'limit' => 1
                ],
                'filter' => [
                    'textSearch' => (string)$cardData['vendorCode'],
                    "withPhoto" => -1
                ]
            ]),
            new UploadImages($seller, $cardData['photos'], $cardData['vendorCode'])
        ])->delay(now()->addMinutes(3))->dispatch();
    }
}
