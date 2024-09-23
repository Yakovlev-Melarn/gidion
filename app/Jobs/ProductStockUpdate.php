<?php

namespace App\Jobs;

use App\Http\Libs\WBMarketplace;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductStockUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    private $seller;
    private $barcode;
    private $stock;

    /**
     * Create a new job instance.
     */
    public function __construct(Seller $seller, $stock, $barcode)
    {
        $this->seller = $seller;
        $this->stock = $stock;
        $this->barcode = $barcode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        WBMarketplace::stockUpdate($this->seller, $this->stock, $this->barcode);
    }
}
