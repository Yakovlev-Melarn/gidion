<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\WBStatistics;
use App\Models\Card;
use App\Models\Seller;
use App\Models\Wbststock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class StatisticsStocks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    private $seller;

    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }

    public function handle(): void
    {
        StatisticsStocks::dispatch($this->seller)->delay(now()->addHours(3));
        try {
            $this->updateWbStocks();
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
        $this->removeByStock();
        $this->returnToSale();
    }

    private function returnToSale()
    {
        $cards = Card::where("seller_id", $this->seller->id)
            ->where("removeByStock", 1)
            ->where("supplier", 10)
            ->get();
        foreach ($cards as $card) {
            if ($card->slstock) {
                if ($card->slstock->is_local) {
                    continue;
                }
            }
            $wbStock = Wbststock::where("nmId", $card->nmID)->where("quantity", '>', '0')->first();
            if (empty($wbStock)) {
                $card->dimensions->width = 10;
                $card->dimensions->height = 10;
                $card->dimensions->length = 10;
                $card->dimensions->save();
                ContentCardDimensions::dispatch($this->seller, $card);
                echo "Возвращаю в продажу товар {$card->title}, nmId = {$card->nmID}\r\n";
                $card->removeByStock = 0;
                $card->save();
                if (!$card->slstock) {
                    CardLib::createEmptyStock($card->id, $this->seller->id);
                    $card = Card::find($card->id);
                }
                $card->slstock->amount = 0;
                $card->slstock->save();
                $card->prices->percent = 2;
                $card->prices->save();
                CalcPrice::dispatch($this->seller, $this->seller->percentageOfMargin, $card->id);
            }
        }
        Bus::chain([
            new PriceUpdate($this->seller),
            new StockUpdate($this->seller)
        ])->dispatch();
    }

    private function removeByStock()
    {
        $request = new Request();
        $stocks = Wbststock::where("seller_id", $this->seller->id)
            ->where('quantity', '>', '0')->get();
        foreach ($stocks as $stock) {
            if (!$stock->card) {
                echo "Нет карточки nmID {$stock->nmId}\r\n";
                continue;
            }
            if ($stock->card->supplier != 10) {
                continue;
            }
            if ($stock->card->removeByStock == 1) {
                continue;
            }
            $card = $stock->card;
            $ssp = CardLib::getSellStockPrice($card->id);
            if(empty($ssp['price'])){
                PriceInfo::dispatch($this->seller);
                continue;
            }
            $card->removeByStock = 0;
            $card->save();
            $request->cardDimensionsWidth = $card->dimensions->width;
            $request->cardDimensionsHeight = $card->dimensions->height;
            $request->cardDimensionsLength = $card->dimensions->length;
            $request->sellPrice = $ssp['price'];
            $request->removeStock = 1;
            $request->discount = $ssp['discount'];
            $request->supplierPrice = $card->prices->s_price;
            ProductStockUpdate::dispatch($this->seller, 0, $stock->barcode);
            CardLib::update($card, $request, $this->seller, false);
            CalcPrice::dispatch($this->seller, $this->seller->percentageOfMargin, $card->id);
        }
        Bus::chain([
            new PriceUpdate($this->seller),
            new StockUpdate($this->seller),
        ])->dispatch();
    }

    private function updateWbStocks()
    {
        Wbststock::where("seller_id", $this->seller->id)->delete();
        $result = WBStatistics::stocks($this->seller);
        foreach ($result as $item) {
            $stock = new Wbststock();
            $stock->fill((array)$item);
            $stock->seller_id = $this->seller->id;
            $stock->save();
        }
    }
}
