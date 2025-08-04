<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\Telegramm;
use App\Http\Libs\WBStatistics;
use App\Models\Card;
use App\Models\Seller;
use App\Models\Wbststock;
use Exception;
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
        } catch (Exception $exception) {
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
                Telegramm::send("Возвращаю в продажу товар {$card->title}, nmId = {$card->nmID}, магазин  {$card->seller->name}", $card->seller->user->id);
                echo "Возвращаю в продажу товар {$card->title}, nmId = {$card->nmID}\r\n";
                if ($card->prices->s_price > 0) {
                    $card->removeByStock = 0;
                    $card->daysOfStock = 0;
                    $card->save();
                }
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
        ])->dispatch();
    }

    private function removeByStock()
    {
        $request = new Request();
        $stocks = Wbststock::where("seller_id", $this->seller->id)
            ->where('quantity', '>', '0')->get();
        foreach ($stocks as $stock) {
            if ($card = $stock->card) {
                $request->cardDimensionsWidth = $card->dimensions->width;
                $request->cardDimensionsHeight = $card->dimensions->height;
                $request->cardDimensionsLength = $card->dimensions->length;
                if (!$stock->card) {
                    Telegramm::send("Нет карточки nmID {$stock->nmId} продавец {$this->seller->name}\r\n", $this->seller->user->id);
                    continue;
                }
                if ($stock->card->supplier != 10) {
                    continue;
                }
                $ssp = CardLib::getSellStockPrice($card->id);
                if (empty($ssp['price'])) {
                    Telegramm::send("Нет закупочной цены {$card->nmID} продавец {$this->seller->name}\r\n", $this->seller->user->id);
                    PriceInfo::dispatch($this->seller, 0, $card->nmID);
                    continue;
                }
                if ($card->removeByStock == 1) {
                    if (strtotime($card->updated_at) < strtotime(date("Y-m-d"))) {
                        $card->daysOfStock += 1;
                        $card->removeByStock = 0;
                        $card->save();
                        //$request->sellPrice = $ssp['price'] - (10 * $card->daysOfStock);
                        $request->sellPrice = ($ssp['s_price'] * 5) - (10 * $card->daysOfStock);
                        echo "Устанавливаю цену в {$request->sellPrice}\r\n";
                        $request->removeStock = 1;
                        $request->discount = $ssp['discount'];
                        $request->supplierPrice = $card->prices->s_price;
                        if (!$card->slstock->is_local) {
                            ProductStockUpdate::dispatch($this->seller, 0, $stock->barcode);
                        }
                        CardLib::update($card, $request, $this->seller, false);
                    }
                    continue;
                }
                $card->removeByStock = 0;
                $card->save();
                //$request->sellPrice = $ssp['price'];
                $request->sellPrice = ($ssp['s_price']*5);
                echo "Устанавливаю цену в {$request->sellPrice}\r\n";
                $request->removeStock = 1;
                $request->discount = $ssp['discount'];
                $request->supplierPrice = $card->prices->s_price;
                ProductStockUpdate::dispatch($this->seller, 0, $stock->barcode);
                CardLib::update($card, $request, $this->seller, false);
            }
        }
        Bus::chain([
            new PriceUpdate($this->seller),
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
