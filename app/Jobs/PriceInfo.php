<?php

namespace App\Jobs;

use App\Http\Libs\WBPrice;
use App\Http\Libs\WBSupplier;
use App\Models\Card;
use App\Models\Price;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PriceInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    private $seller;
    private $offset;
    private $nmId;

    /**
     * Create a new job instance.
     */
    public function __construct(Seller $seller, $offset = 0, $nmId = null)
    {
        $this->seller = $seller;
        $this->offset = $offset;
        $this->nmId = $nmId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = WBPrice::priceList($this->seller, $this->offset, $this->nmId);
        if (!empty($response)) {
            foreach ($response as $item) {
                if (!$card = Card::where("nmID", $item['nmID'])->first()) {
                    continue;
                }
                if (!$price = Price::where("card_id", $card->id)->first()) {
                    $price = new Price();
                    $price->card_id = $card->id;
                    $price->seller_id = $this->seller->id;
                    $price->nmId = $item['nmID'];
                    $price->price = $item['sizes'][0]['price'];
                    $price->discount = $item['discount'];
                    $price->save();
                } else {
                    if ($price->price != $item['sizes'][0]['price'] || $price->discount != $item['discount']) {
                        $price->price = $item['sizes'][0]['price'];
                        $price->discount = $item['discount'];
                        $price->save();
                    }
                }
            }
            if(empty($this->nmId)) {
                $offset = $this->offset + 1000;
                PriceInfo::dispatch($this->seller, $offset);
            }
        }
    }
}
