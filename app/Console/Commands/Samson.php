<?php

namespace App\Console\Commands;

use App\Jobs\SamsonCatalogJob;
use App\Jobs\SamsonStockUpdate;
use App\Models\Seller;
use Illuminate\Console\Command;

class Samson extends Command
{
    protected $signature = 'samson:Stock';
    protected $description = 'Command description';

    public function handle()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            SamsonStockUpdate::dispatch($seller)->onQueue('updatesamsonstock');
        }
    }

}
