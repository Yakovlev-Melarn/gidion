<?php

namespace App\Console\Commands;

use App\Jobs\PriceInfo;
use App\Models\Seller;
use Illuminate\Console\Command;

class Prices extends Command
{
    protected $signature = 'prices:Info';
    protected $description = 'Command description';

    public function handle()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            PriceInfo::dispatch($seller);
        }
    }
}
