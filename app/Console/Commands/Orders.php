<?php

namespace App\Console\Commands;

use App\Jobs\MPOrders;
use Illuminate\Console\Command;

class Orders extends Command
{
    protected $signature = 'marketplace:Orders {shipmentId?}';
    protected $description = 'Command description';

    public function handle()
    {
        $shipmentId = $this->argument('shipmentId');
        MPOrders::dispatch($shipmentId);
    }

}
