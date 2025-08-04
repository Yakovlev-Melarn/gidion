<?php

namespace App\Jobs;

use App\Http\Libs\WBContent;
use App\Models\Card;
use App\Models\MarketplaceOrder;
use App\Models\Seller;
use App\Models\Wbststock;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Resale implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 10;
    public $uniqueFor = 360000;
    public $timeout = 360000;

    public function handle(): void
    {
        $cards = Card::where("supplier", 10)
            ->where("removeByStock", 0)
            ->where('createdAt', '<', now()->subMonth()->toDateString())
            ->get();
        foreach ($cards as $card) {
            if ($this->validateResaleCard($card)) {
                $seller = Seller::find($card->seller_id);
                echo "Отправляю в корзину товар {$card->nmID} продавец {$seller->name} \r\n";
                WBContent::trash($seller, [$card->nmID]);
                $supplierSku = $card->supplierSku;
                echo "Удаляю из базы товар {$card->nmID} продавец {$seller->name} \r\n";
                $card->delete();
                $newSeller = $seller = Seller::where('id', '!=', $card->seller_id)->first();
                echo "Определяю нового продавца {$newSeller->name} \r\n";
                CopyCards::copyOneCard($supplierSku, $newSeller);
                echo "Копирую карточку {$supplierSku} для продавца {$newSeller->name} \r\n";
            }
        }
    }

    private function validateResaleCard(Card $card)
    {
        $date = Carbon::now()->subDays(40)->toDateTimeString();
        if ($card->slstock->is_local) {
            echo "Товар {$card->nmID} на локальном остатке \r\n";
            return false;
        }
        if (MarketplaceOrder::where('nmId', $card->nmID)->where("created_at", '>', $date)->first()) {
            echo "Товар {$card->nmID} на продаже \r\n";
            return false;
        }
        if (Wbststock::where('nmId', $card->nmID)->where('quantityFull', '>', 0)->count()) {
            echo "Товар {$card->nmID} на остатке WB\r\n";
            return false;
        }
        return true;
    }
}
