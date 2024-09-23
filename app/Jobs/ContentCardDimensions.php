<?php

namespace App\Jobs;

use App\Http\Libs\WBContent;
use App\Models\Card;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ContentCardDimensions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    private $seller;
    private $card;

    /**
     * Create a new job instance.
     */
    public function __construct(Seller $seller, Card $card)
    {
        $this->seller = $seller;
        $this->card = $card;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        WBContent::updateCardDimensions($this->seller, $this->card);
    }
}
