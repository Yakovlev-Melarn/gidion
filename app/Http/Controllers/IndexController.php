<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Libs\WBStatistics;
use App\Jobs\StatisticsStocks;
use App\Models\MarketplaceOrder;
use App\Models\User;
use App\Models\Wbstorder;
use App\Models\Wbstsale;
use App\Models\Wbststock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request, $date = null)
    {
        if (empty($date)) {
            $date = Carbon::today()->toDateTimeString();
        }
        $ordersSt = $this->charts($request, $date);
        $stocks = Wbststock::where("seller_id", session()->get('sellerId'))
            ->where('quantity', '>', 0);
        $inWayToClient = Wbststock::where("seller_id", session()->get('sellerId'))
            ->where('inWayToClient', '>', 0);
        $inWayFromClient = Wbststock::where("seller_id", session()->get('sellerId'))
            ->where('inWayFromClient', '>', 0);
        $inWayToClientPrice = 0;
        foreach ($inWayToClient->get() as $product) {
            $inWayToClientPrice += $product->Price;
        }
        $inWayFromClientPrice = 0;
        foreach ($inWayFromClient->get() as $product) {
            $inWayFromClientPrice += $product->Price;
        }
        $stockPrice = 0;
        foreach ($stocks->get() as $item) {
            if ($item->card) {
                if ($item->card->prices) {
                    $stockPrice += $item->card->prices->price;
                }
            }
        }
        return view('Index/index', [
            'productOnWh' => $stocks->count(),
            'stockPrice' => $stockPrice,
            'inWayToClientPrice' => $inWayToClientPrice,
            'inWayToClient' => $inWayToClient->count(),
            'inWayFromClient' => $inWayFromClient->count(),
            'inWayFromClientPrice' => $inWayFromClientPrice,
            'ordersCount' => $ordersSt['ordersCount'],
            'ordersSum' => $ordersSt['orderSum'],
            'salesCount' => $ordersSt['salesCount'],
            'salesSum' => $ordersSt['salesSum'],
            'selectedDate' => $date
        ]);
    }

    public function charts(Request $request, $date = null)
    {
        if (!empty($request->date)) {
            $date = $request->date;
        } else {
            if (empty($date)) {
                $date = Carbon::today()->toDateTimeString();
            }
        }
        $nextDay = Carbon::createFromFormat("Y-m-d H:i:s", $date)->addDay()->toDateTimeString();
        $ordersData = $salesData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $ordersCount = $salesCount = 0;
        $ordersSum = $salesSum = 0;
        $sales = Wbstsale::where("lastChangeDate", '>', $date)
            ->where("lastChangeDate", '<', $nextDay)
            ->where("seller_id", session()->get('sellerId'))->get();
        foreach ($sales as $sale) {
            $salesCount++;
            $hour = explode(' ', $sale->lastChangeDate);
            $hour = explode(':', $hour[1]);
            $salesData[(int)$hour[0]] += $sale->finishedPrice;
            $salesSum += $sale->finishedPrice;
        }
        $orders = Wbstorder::where("lastChangeDate", '>', $date)
            ->where("lastChangeDate", '<', $nextDay)
            ->where("seller_id", session()->get('sellerId'))->where('isCancel', 0)->get();
        foreach ($orders as $order) {
            $ordersCount++;
            $hour = explode(' ', $order->date);
            $hour = explode(':', $hour[1]);
            $ordersData[(int)$hour[0]] += $order->finishedPrice;
            $ordersSum += $order->finishedPrice;
        }
        $orders = MarketplaceOrder::where("created_at", '>', $date)
            ->where("created_at", '<', $nextDay)
            ->where("seller_id", session()->get('sellerId'))->get();
        foreach ($orders as $order) {
            if (Wbstorder::where("srid", $order->rid)->first()) {
                continue;
            }
            $ordersCount++;
            $hour = explode(' ', $order->created_at);
            $hour = explode(':', $hour[1]);
            $ordersData[(int)$hour[0]] += $order->convertedPrice / 100;
            $ordersSum += $order->convertedPrice / 100;
        }
        $stepSize = 100;
        if ($ordersSum > 1000 || $salesSum > 1000) {
            $stepSize = 500;
        }
        if ($ordersSum > 5000 || $salesSum > 5000) {
            $stepSize = 1000;
        }
        if ($ordersSum > 10000 || $salesSum > 10000) {
            $stepSize = 3000;
        }
        return [
            'orders' => $ordersData,
            'ordersCount' => $ordersCount,
            'orderSum' => $ordersSum,
            'sales' => $salesData,
            'salesCount' => $salesCount,
            'salesSum' => $salesSum,
            'stepSize' => $stepSize
        ];
    }
}
