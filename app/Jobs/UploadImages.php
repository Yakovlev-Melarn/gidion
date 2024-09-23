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

    public function __construct(Seller $seller, array $photos, $vendorCode, $reload = 0)
    {
        $this->seller = $seller;
        $this->photos = $photos;
        $this->vendorCode = $vendorCode;
        $this->reload = $reload;
    }

    public function handle(): void
    {
        if(!$this->reload) {
            $card = Card::where('vendorCode', '=', $this->vendorCode)->first();
            print_r(WBContent::uploadPhotos($this->seller, $this->photos, $card->nmID));
        } else {
            //@todo....
        }
    }
}
