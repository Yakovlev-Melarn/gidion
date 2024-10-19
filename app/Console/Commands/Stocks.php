<?php

namespace App\Console\Commands;

use App\Jobs\StatisticsStocks;
use App\Models\Seller;
use Illuminate\Console\Command;

class Stocks extends Command
{
    protected $signature = 'statistics:Stocks';
    protected $description = 'Command description';

    public function handle()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            StatisticsStocks::dispatch($seller);
        }
    }
}
