<?php

namespace App\Jobs;

use App\Http\Libs\WBPrice;
use App\Models\Price;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PriceUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $uniqueFor = 3600;
    public int $timeout = 3600;
    private Seller $seller;


    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }

    public function handle(): void
    {
        do {
            $priceList = Price::where("toUpload", 1)
                ->where("seller_id", $this->seller->id)
                ->limit(999)
                ->orderBy('id')
                ->get();
            $prices['data'] = [];
            foreach ($priceList as $item) {
                $prices['data'][] = [
                    'nmID' => (int)$item->card->nmID,
                    'price' => (int)$item->price
                ];
            }
            if (!empty($prices['data'])) {
                $result = WBPrice::pricesUpdate($this->seller, $prices);
                if (!empty($result['errors'])) {
                    print_r($prices);
                    return;
                }
                Price::where("toUpload", 1)
                    ->where("seller_id", $this->seller->id)
                    ->limit(999)
                    ->orderBy('id')
                    ->update(['toUpload' => 0]);
            }
        } while (!empty($prices['data']));
    }
}
