<?php

namespace App\Jobs;

use App\Http\Libs\WBContent;
use App\Models\Card;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class UploadImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    public Seller $seller;
    public array $photos;
    private $vendorCode;
    private $reload;

    public function __construct(Seller $seller, array $photos, $vendorCode, $reload = 1)
    {
        $this->seller = $seller;
        $this->photos = $photos;
        $this->vendorCode = $vendorCode;
        $this->reload = $reload;
    }

    public function handle(): void
    {
        if ($card = Card::where('vendorCode', '=', $this->vendorCode)->where("seller_id", '=', $this->seller->id)->first()) {
            WBContent::uploadPhotos($this->seller, $this->photos, $card->nmID);
        } else {
            if ($this->reload) {
                Bus::chain([
                    new ContentCard($this->seller, [
                        'cursor' => [
                            'limit' => 1
                        ],
                        'filter' => [
                            'textSearch' => (string)$this->vendorCode,
                            "withPhoto" => -1
                        ]
                    ]),
                    new UploadImages($this->seller, $this->photos, $this->vendorCode, 0)
                ])->dispatch();
            }
        }
    }
}
