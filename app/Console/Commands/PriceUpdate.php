<?php

namespace App\Console\Commands;

use App\Jobs\PriceUpdate as PU;
use App\Models\Seller;
use Illuminate\Console\Command;

class PriceUpdate extends Command
{
    protected $signature = 'price:Update';
    protected $description = 'Command description';

    public function handle()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            PU::dispatch($seller);
        }
    }
}
