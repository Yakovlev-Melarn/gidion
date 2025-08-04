<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\Helper;
use App\Http\Libs\WBMarketplace;
use App\Models\Card;
use App\Models\SamsonStock;
use App\Models\Seller;
use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SamsonStockUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $uniqueFor = 360000;
    public int $timeout = 360000;
    private Seller $seller;
    private const NULL_STOCK = true;


    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }

    public function handle(): void
    {
        $this->prepareAmount();
        $this->prepareUpload();
        if (!self::NULL_STOCK) {
            SamsonStockUpdate::dispatch($this->seller)->onQueue('updatesamsonstock')->delay(now()->addHour());
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
        if (($card->slstock->amount != $amount) || ($amount == 0)) {
            $card->slstock->amount = $amount;
            $card->slstock->toUpload = 1;
            $card->slstock->save();
        }
    }

    private function getStocks($url)
    {
        if (empty($url)) {
            $url = "https://api.samsonopt.ru/v1/sku/stock?api_key=c63363e9e46de524234f80de15711aee";
        }
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
        $result = Http::withHeaders([
            'User-Agent' => $agent,
            'Accept' => 'application/json',
            'Accept-Encoding' => 'gzip'
        ])->get($url);
        $result = $result->json();
        if (empty($result['data'])) {
            return true;
        }
        foreach ($result['data'] as $item) {
            $amount = Helper::arrSearch($item['stock_list'], 'type', 'idp');
            $stock = new SamsonStock();
            $stock->sku = $item['sku'];
            $stock->amount = $amount;
            $stock->save();
        }
        if (!empty($result['meta']['pagination']['next'])) {
            $this->getStocks($result['meta']['pagination']['next']);
        }
    }

    private function prepareAmount($url = null)
    {
        $cards = Card::where("seller_id", $this->seller->id)
            ->where("supplier", 2);
        if (!self::NULL_STOCK) {
            $cards = $cards->where("detailedStockAt", '<', date('Y-m-d'));
        }
        $cards = $cards->get();
        if ($cards->count() > 0) {
            if (!SamsonStock::count() || SamsonStock::where("created_at", '<', date('Y-m-d'))->first()) {
                SamsonStock::truncate();
                $this->getStocks($url);
            }
        }
        foreach ($cards as $card) {
            if (self::NULL_STOCK) {
                $this->saveStock($card, 0);
                continue;
            }
            $card->detailedStockAt = date('Y-m-d H:i:s');
            $card->save();
            if (!$amount = SamsonStock::where('sku', $card->supplierSku)->first()) {
                $this->saveStock($card, 0);
                continue;
            }
            $this->saveStock($card, $amount->amount);
        }
    }
}
