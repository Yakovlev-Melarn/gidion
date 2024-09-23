<?php

namespace App\Jobs;

use App\Http\Libs\CardLib;
use App\Http\Libs\WBStatistics;
use App\Models\Card;
use App\Models\Seller;
use App\Models\Wbstorder;
use App\Models\Wbststock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StatisticsOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $uniqueFor = 3600;
    public $timeout = 3600;
    private $seller;

    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }

    public function handle(): void
    {
        StatisticsOrders::dispatch($this->seller)->delay(now()->addHour());
        $result = WBStatistics::orders($this->seller);
        foreach ($result as $item) {
            if (!$order = Wbstorder::where('srid', $item['srid'])->first()) {
                $order = new Wbstorder();
                $order->fill($item);
                $order->seller_id = $this->seller->id;
                $order->save();
            }
        }
    }
}
