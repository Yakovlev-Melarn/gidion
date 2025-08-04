<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\WBContent;
use App\Http\Libs\WBSupplier;
use App\Models\Card;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCardsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 15;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    private $seller;
    private $settings;

    /**
     * Create a new job instance.
     */
    public function __construct(Seller $seller, $settings)
    {
        $this->seller = $seller;
        $this->settings = $settings;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $result = WBContent::cardsList($this->seller, $this->settings);
        foreach ($result['cards'] as $resultCard) {
            if (!$card = Card::where("nmID", $resultCard['nmID'])->first()) {
                $addedCard = CardLib::addCard($resultCard, $this->seller);
                echo "Добавлена карточка nmID {$addedCard->nmID}\r\n";
            } else {
                if (empty($card->origSku)) {
                    $detail = WBSupplier::getCardInfo($card->supplierSku);
                    if (!empty($detail['vendor_code'])) {
                        $card->origSku = $detail['vendor_code'];
                    }
                }
                $card->syncStatus = 1;
                $card->save();
            }
        }
        if (!isset($result['cursor']['updatedAt'])) {
            return;
        }
        $this->settings = [
            'cursor' => [
                'limit' => 100,
                'updatedAt' => $result['cursor']['updatedAt'],
                'nmID' => $result['cursor']['nmID']
            ],
            'filter' => [
                "withPhoto" => -1
            ]
        ];
        SyncCardsJob::dispatch($this->seller, $this->settings);
    }
}
