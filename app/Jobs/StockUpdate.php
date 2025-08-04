<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\Helper;
use App\Http\Libs\WBMarketplace;
use App\Http\Libs\WBSupplier;
use App\Models\Card;
use App\Models\Price;
use App\Models\Seller;
use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StockUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 100;
    public int $uniqueFor = 360000;
    public int $timeout = 360000;
    private Seller $seller;
    private const NULL_STOCK = false;


    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }

    public function handle(): void
    {
        $detailed = $this->prepareAmount();
        $this->prepareUpload();
        if (!self::NULL_STOCK) {
            if (!$detailed) {
                StockUpdate::dispatch($this->seller)->onQueue('updatestock')->delay(now()->addHour());
            } else {
                StockUpdate::dispatch($this->seller)->onQueue('updatestock');
            }
        }
        PriceUpdate::dispatch($this->seller);
    }

    private function prepareUpload()
    {
        $offset = 0;
        do {
            $data = [];
            $stocks = Stock::where('toUpload', 1)
                ->where('seller_id', $this->seller->id)
                ->limit(300)
                ->offset($offset)
                ->get();
            $offset += 300;
            if ($stocks->count() > 0) {
                foreach ($stocks as $stock) {
                    if (!empty($stock->sizes)) {
                        $data[] = [
                            'sku' => (string)$stock->sizes->skus,
                            'amount' => $stock->amount
                        ];
                    } else {
                        ContentCard::dispatch($this->seller, [
                            'cursor' => [
                                'limit' => 1
                            ],
                            'filter' => [
                                'textSearch' => (string)$stock->card->vendorCode,
                                "withPhoto" => -1
                            ]
                        ], 'addSize');
                    }
                }
                WBMarketplace::stocksUpdate($this->seller, $data);
            }
        } while ($stocks->count() > 0);
        Stock::where('toUpload', 1)->where('seller_id', $this->seller->id)->update(['toUpload' => 0]);
    }

    private function saveStock(Card $card, $amount)
    {
        if (empty($card->slstock)) {
            CardLib::createEmptyStock($card->id, $this->seller->id);
            $card = Card::find($card->id);
        }
        if ($card->slstock->is_local) {
            return;
        }
        $card->slstock->amount = 0;
        $card->slstock->toUpload = 1;
        $card->slstock->save();
        if (($card->slstock->amount != $amount) || ($amount == 0)) {
            $card->slstock->amount = $amount;
            $card->slstock->toUpload = 1;
            $card->slstock->save();
        }
    }

    private function prepareAmount()
    {
        $detailed = 0;
        $offset = 0;
        do {
            $cards = Card::where("seller_id", $this->seller->id)
                ->where("supplier", 10)
                ->where("removeByStock", 0);
            if (!self::NULL_STOCK) {
                $cards = $cards->where("detailedStockAt", '<', date('Y-m-d'));
            }
            $cards = $cards->limit(300)
                ->offset($offset)
                ->get();
            $offset += 300;
            $supplierSkus = [];
            if ($cards->count()) {
                $nmIds = [];
                foreach ($cards as $card) {
                    /*if (empty($card->prices)) {
                        continue;
                    }
                    if ($card->prices->s_price < 4000) {
                        continue;
                    }
                    echo "{$card->supplierSku}\r\n";
                    if (self::NULL_STOCK) {
                        $this->saveStock($card, 0);
                        continue;
                    }*/
                    $nmIds[] = $card->nmID;
                    $supplierSkus[] = $card->supplierSku;
                }
                //Trash::dispatch($this->seller,$nmIds);
                if (!empty($supplierSkus)) {
                    $supplierSkus = implode(';', $supplierSkus);
                    $results = WBSupplier::getAmounts($supplierSkus);
                    $supplierSkus = explode(';', $supplierSkus);
                    if ($results) {
                        echo "Есть результат! Осталось обработать " . count($supplierSkus) . "\r\n";
                        foreach ($results as $supplierSku => $amount) {
                            $card = Card::where("supplierSku", $supplierSku)->first();
                            $card->detailedStockAt = date('Y-m-d H:i:s');
                            $card->save();
                            if (empty($card->prices)) {
                                $prices = new Price();
                                $prices->card_id = $card->id;
                                $prices->seller_id = $card->seller_id;
                                $prices->nmId = $card->nmID;
                                $prices->price = 10000;
                                $prices->discount = 0;
                                $prices->save();
                                $card = Card::where("supplierSku", $supplierSku)->first();
                            }
                            if ((int)$card->prices->s_price === 0) {
                                $card->prices->s_price = WBSupplier::getPrice($card->supplierSku);
                                $card->prices->save();
                                $card = Card::where("supplierSku", $supplierSku)->first();
                                echo "sPrTo {$card->id} = {$card->prices->s_price}\r\n";
                            }
                            if ($card->prices->s_price > 3999) {
                                $this->saveStock($card, 10); //(!!!)
                                $card->prices->price = ceil($card->prices->s_price / 2);
                                $card->prices->toUpload = 1;
                                $card->prices->save();
                                $detailed++;
                            } else {
                                $this->saveStock($card, 0);
                                $detailed++;
                            }
                            $k = array_search($supplierSku, $supplierSkus);
                            unset($supplierSkus[$k]);
                            echo "Есть результат! Осталось обработать " . count($supplierSkus) . "\r\n";
                        }
                    }
                    foreach ($supplierSkus as $key => $supplierSku) {
                        $card = Card::where("supplierSku", $supplierSku)->first();
                        $card->detailedStockAt = date('Y-m-d H:i:s');
                        $card->save();
                        $this->saveStock($card, 0);
                        $detailed++;
                        unset($supplierSkus[$key]);
                        echo "НЕТ результата! Осталось обработать " . count($supplierSkus) . "\r\n";
                    }
                }
            }
        } while ($cards->count() > 0);
        return $detailed;
    }
}
