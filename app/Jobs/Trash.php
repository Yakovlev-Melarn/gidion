<?php

namespace App\Jobs;

use App\Http\Libs\WBContent;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Trash implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    public Seller $seller;
    public array $nmIds;

    public function __construct(Seller $seller, array $nmIds)
    {
        $this->seller = $seller;
        $this->nmIds = $nmIds;
    }

    public function handle(): void
    {
        print_r(WBContent::Trash($this->seller, $this->nmIds));
    }
}
