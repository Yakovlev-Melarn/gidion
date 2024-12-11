<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\WBMarketplace;
use App\Http\Libs\WBSupplier;
use App\Models\Card;
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


    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }

    public function handle(): void
    {
        //$this->prepareAmount();
        $detailed = $this->prepareAmount();
        $this->prepareUpload();
        if (!$detailed) {
            StockUpdate::dispatch($this->seller)->onQueue('updatestock')->delay(now()->addHour());
        } else {
            StockUpdate::dispatch($this->seller)->onQueue('updatestock');
        }
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
                ->where("removeByStock", 0)
                ->where("detailedStockAt", '<', date('Y-m-d'))
                ->limit(300)
                ->offset($offset)
                ->get();
            $offset += 300;
            $supplierSkus = [];
            if ($cards->count()) {
                foreach ($cards as $card) {
                    /*$this->saveStock($card, 0);
                    continue;*/
                    $supplierSkus[] = $card->supplierSku;
                }
                if (!empty($supplierSkus)) {
                    $supplierSkus = implode(';', $supplierSkus);
                    if ($results = WBSupplier::getAmounts($supplierSkus)) {
                        $supplierSkus = explode(';', $supplierSkus);
                        foreach ($results as $supplierSku => $amount) {
                            $card = Card::where("supplierSku", $supplierSku)->first();
                            $card->detailedStockAt = date('Y-m-d H:i:s');
                            $card->save();
                            $this->saveStock($card, $amount);
                            $detailed++;
                            $k = array_search($supplierSku, $supplierSkus);
                            unset($supplierSkus[$k]);
                        }
                        foreach ($supplierSkus as $key => $supplierSku) {
                            $card = Card::where("supplierSku", $supplierSku)->first();
                            $this->saveStock($card, 0);
                            $detailed++;
                            unset($supplierSkus[$key]);
                        }
                    }
                }
            }
        } while ($cards->count() > 0);
        return $detailed;
    }
}
