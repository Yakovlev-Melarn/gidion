<?php

namespace App\Jobs;

use App\Http\Libs\WBContent;
use App\Http\Libs\WBSupplier;
use App\Models\Card;
use App\Models\Seller;
use App\Models\Wbststock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrigSkusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 10;
    public $uniqueFor = 3600;
    public $timeout = 3600;

    public function handle(): void
    {
        $cards = Card::where("supplier", 10)
            ->whereNull("origSku")
            ->get();
        foreach ($cards as $card) {
            $detail = WBSupplier::getCardInfo($card->supplierSku);
            if (!empty($detail['vendor_code'])) {
                $card->origSku = $detail['vendor_code'];
            }
            $card->save();
        }
    }
}
