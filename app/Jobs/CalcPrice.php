<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\WBSupplier;
use App\Models\Card;
use App\Models\Price;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalcPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    public Seller $seller;
    public int $percent;
    public int $cardId;

    public function __construct(Seller $seller, int $percent, $cardId = 0)
    {
        $this->seller = $seller;
        $this->percent = $percent;
        $this->cardId = $cardId;
    }

    public function handle(): void
    {
        $prices = Price::where("seller_id", $this->seller->id);
        $prices = $prices->where("holdPrice", '=', 0);
        if ($this->cardId) {
            $prices = $prices->where('card_id', '=', $this->cardId);
        } else {
            $prices = $prices->where("percent", '!=', $this->percent);
        }
        $prices = $prices->limit(300)
            ->get();
        if ($prices->count() > 0) {
            $this->getSPrices($prices);
            foreach ($prices as $price) {
                $commission = CardLib::getComission($price->card);
                if ($this->checkPrice($price, $commission)) {
                    $this->calc($price, $commission);
                } else {
                    $price->holdPrice = 1;
                    $price->save();
                }
            }
            CalcPrice::dispatch($this->seller, $this->percent, $this->cardId)->onQueue('updateprice');
        } else {
            Price::where("holdPrice", '=', 1)->update(['holdPrice' => 0]);
        }
        PriceUpdate::dispatch($this->seller);
    }

    public function calc($price, $commission)
    {
        $price = Price::find($price->id);
        if (!empty($price->s_price)) {
            $price->s_price = WBSupplier::getPrice($price->card->supplierSku);
            $price->save();
        }
        if (!empty($price->s_price)) {
            if ($this->percent == 1) {
                $price->percent = ceil($commission);
            } else {
                $price->percent = $this->percent;
            }
            $price->price = ceil($price->s_price * 5);
            if ($this->cardId) {
                $price->holdPrice = 1;
                $price->holdedAt = date("Y-m-d H:i:s");
            }
            $price->toUpload = 1;
            $price->save();
        }
    }

    private function checkPrice($price, $commission)
    {
        if ($price->card->supplier != 10 && $price->card->supplier != 11) {
            return false;
        }
        if ($price->card->removeByStock == 1) {
            return false;
        }
        if ($price->card->slstock) {
            if ($price->card->slstock->is_local) {
                return false;
            }
        }
        if ($this->percent > 1 && $price->percent == $this->percent) {
            return false;
        }
        if ($this->percent == 1 && ($price->percent == $commission)) {
            return false;
        }
        if (empty($price->s_price)) {
            return false;
        }
        return true;
    }

    private function getSPrices($prices)
    {
        $nmIds = [];
        foreach ($prices as $price) {
            if ($price->card->supplier != 10) {
                continue;
            }
            if (!empty($price->card->supplierSku)) {
                $nmIds[] = $price->card->supplierSku;
            }
        }
        $nmIds = implode(';', $nmIds);
        if ($result = WBSupplier::getPrices($nmIds,$this->seller)) {
            foreach ($result as $supplierSku => $price) {
                $card = Card::where("supplierSku", $supplierSku)->first();
                if ($card->prices->s_price != $price) {
                    if($price) {
                        $card->prices->s_price = $price;
                        $card->prices->save();
                    }
                }
            }
        }
    }
}
