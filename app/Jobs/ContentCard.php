<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\WBContent;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ContentCard implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $action;
    private $seller;
    private $settings;
    public $timeout = 3600;
    public $uniqueFor = 3600;

    public function __construct(Seller $seller, $settings, $action='addCard')
    {
        $this->seller = $seller;
        $this->settings = $settings;
        $this->action = $action;
    }

    public function handle(): void
    {
        $result = WBContent::cardsList($this->seller, $this->settings);
        if (empty($result['cards'])) {
            throw new \Exception(json_encode($result,256));
        }
        foreach ($result['cards'] as $cardData) {
            CardLib::{$this->action}($cardData, $this->seller);
        }
    }
}
