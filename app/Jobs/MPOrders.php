<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\Telegramm;
use App\Http\Libs\WBMarketplace;
use App\Http\Libs\WBSupplier;
use App\Models\MarketplaceOrder;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MPOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    public $shipmentId;

    public function __construct($shipment = null)
    {
        $this->shipmentId = $shipment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->shipmentId)) {
            MPOrders::dispatch(null)->delay(now()->addMinutes(5));
        }
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            if (!empty($this->shipmentId)) {
                //MarketplaceOrder::where("shipmentId", $this->shipmentId)->delete();
                $shipment['id'] = $this->shipmentId;
                $orders = WBMarketplace::getOrders($seller, $shipment['id']);
            } else {
                $shipment = self::getShipment($seller);
                $orders = WBMarketplace::getNewOrders($seller);
            }
            if (!empty($orders['orders'])) {
                $this->addOrders($seller, $orders, $shipment);
            }
            $this->getQrCodes($seller, $shipment);
        }
    }

    private function getQrCodes($seller, $shipment)
    {
        $orders = MarketplaceOrder::where("shipmentId", $shipment['id'])
            ->where("qrcode", '0')->orWhereNull('bincode')->limit(50)->get();
        $orderIds = [];
        foreach ($orders as $order) {
            $orderIds[] = (int)$order->orderId;
        }
        $qrcodes = WBMarketplace::getQrCode($seller, $orderIds);
        if (!empty($qrcodes['stickers'])) {
            foreach ($qrcodes['stickers'] as $sticker) {
                $order = MarketplaceOrder::where("orderId", $sticker['orderId'])->first();
                $order->partA = $sticker['partA'];
                $order->partB = $sticker['partB'];
                $order->qrcode = $sticker['barcode'];
                $order->bincode = $sticker['file'];
                $order->save();
            }
        }
    }

    private function addOrders($seller, $orders, $shipment)
    {
        foreach ($orders['orders'] as $mporder) {
            if (!$order = MarketplaceOrder::where("orderId", $mporder['id'])->first()) {
                $order = new MarketplaceOrder();
                $order->orderId = $mporder['id'];
                $order->warehouseId = $mporder['warehouseId'];
                $order->nmId = $mporder['nmId'];
                $order->chrtId = $mporder['chrtId'];
                $order->price = $mporder['price'];
                $order->convertedPrice = $mporder['convertedPrice'];
                $order->currencyCode = $mporder['currencyCode'];
                $order->convertedCurrencyCode = $mporder['convertedCurrencyCode'];
                $order->cargoType = $mporder['cargoType'];
                if (isset($mporder['address'])) {
                    $order->address = $mporder['address'];
                }
                if (isset($mporder['requiredMeta'])) {
                    $order->requiredMeta = json_encode($mporder['requiredMeta'], JSON_UNESCAPED_UNICODE);
                }
                if (isset($mporder['deliveryType'])) {
                    $order->deliveryType = $mporder['deliveryType'];
                }
                if(isset($mporder['user'])) {
                    $order->user = $mporder['user'];
                }
                $order->orderUid = $mporder['orderUid'];
                $order->article = $mporder['article'];
                $order->rid = $mporder['rid'];
                $order->createdAt = $mporder['createdAt'];
                $order->offices = json_encode($mporder['offices'], JSON_UNESCAPED_UNICODE);
                $order->skus = $mporder['skus'][0];
                $order->shipmentId = $shipment['id'];
                $order->seller_id = $seller->id;
                if (!empty($this->shipmentId)) {
                    $order->status = 0;
                } else {
                    $convertedPriceRub = $order->convertedPrice / 100;
                    if (!$order->card) {
                        ContentCard::dispatch($seller, [
                            'cursor' => [
                                'limit' => 1
                            ],
                            'filter' => [
                                'textSearch' => (string)$order->nmId,
                                "withPhoto" => -1
                            ]
                        ]);
                        continue;
                    }
                    if ($order->card->cardsupplier) {
                        $supplier = $order->card->cardsupplier->name;
                    } else {
                        $supplier = "Неизвестен";
                    }
                    if ($order->card->supplier == 10) {
                        if (!$order->card->slstock) {
                            CardLib::createEmptyStock($order->card->id, $seller->id);
                        } else {
                            if (!$order->card->slstock->is_local) {
                                if ($results = WBSupplier::getAmounts($order->card->supplierSku)) {
                                    foreach ($results as $supplierSku => $amount) {
                                        if ($supplierSku == $order->card->supplierSku) {
                                            $order->card->slstock->amount = $amount;
                                            $order->card->slstock->toUpload = 1;
                                            $order->card->slstock->save();
                                        }
                                    }
                                }
                            }
                        }
                        $supplier = "{$supplier}\r\nhttps://www.wildberries.ru/catalog/{$order->card->supplierSku}/detail.aspx?targetUrl=GP\r\nOZON: https://www.ozon.ru/search/?from_global=true&text={$order->card->origSku}";
                    }
                    if ($order->card->supplier == 11) {
                            $supplier = "{$supplier}\r\nhttps://office-burg.ru/search/?q={$order->card->supplierSku}\r\nOZON: https://www.ozon.ru/search/?from_global=true&text={$order->card->origSku}";
                    }
                    Telegramm::send("Новый заказ\r\n
                    Магазин: {$seller->name}\r\n
                    Товар: {$order->card->title}\r\n
                    Поставщик: {$supplier}\r\n
                    Стоимость: {$convertedPriceRub}\r\n");
                }
                $order->save();
            }
            if (empty($this->shipmentId)) {
                WBMarketplace::addOrderToShipment($seller, $order->orderId, $shipment['id']);
            }
        }
    }

    public static function getShipment($seller)
    {
        if (!$shipment = WBMarketplace::getOpenSupplies($seller)) {
            WBMarketplace::createSupplies($seller);
            sleep(5);
            $shipment = WBMarketplace::getOpenSupplies($seller);
        }
        if (!$shipment['id']) {
            throw new \Exception("Нет созданой поставки!");
        }
        return $shipment;
    }
}
