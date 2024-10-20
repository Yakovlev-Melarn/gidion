<?php

namespace App\Http\Controllers;

use App\Http\Libs\CardLib;
use App\Http\Libs\WBContent;
use App\Http\Libs\WBMarketplace;
use App\Jobs\CalcPrice;
use App\Jobs\ContentCard;
use App\Jobs\MPOrders;
use App\Jobs\PriceInfo;
use App\Jobs\StockUpdate;
use App\Models\Bartender;
use App\Models\Card;
use App\Models\MarketplaceOrder;
use App\Models\Seller;
use App\Models\Wbstorder;
use App\Models\Wbstsale;
use App\Models\Wbststock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    const ORDER = 1;
    const SALE = 2;

    private function ordersale($type, $date)
    {
        if (empty($date)) {
            $date = Carbon::today()->toDateTimeString();
        } else {
            $date = date("Y-m-d H:i:s", strtotime($date));
        }
        $nextDay = Carbon::createFromFormat("Y-m-d H:i:s", $date)->addDay()->toDateTimeString();
        if ($type == self::ORDER) {
            $orders = Wbstorder::where("date", '>', $date)
                ->where("date", '<', $nextDay)
                ->where("seller_id", session()->get('sellerId'))->where('isCancel', 0);
        } else {
            $orders = Wbstsale::where("lastChangeDate", '>', $date)
                ->where("lastChangeDate", '<', $nextDay)
                ->where("seller_id", session()->get('sellerId'));
        }
        $orders = $orders->get();
        $cards = [];
        $prices = [];
        $mp = [];
        foreach ($orders as $order) {
            $card = Card::where("nmID", '=', $order->nmId)->first();
            if (!empty($card->nmID)) {
                $cards[$order->srid] = $card;
                $prices[$order->srid] = $order->finishedPrice;
                if ($order->orderType == 'Клиентский') {
                    $mp[$order->srid] = 0;
                } else {
                    $mp[$order->srid] = $order->orderType;
                }
            }
        }
        if ($type == self::ORDER) {
            $orders = MarketplaceOrder::where("created_at", '>', $date)
                ->where("created_at", '<', $nextDay)
                ->where("seller_id", session()->get('sellerId'))->get();
            foreach ($orders as $order) {
                $card = Card::where("nmID", '=', $order->nmId)->first();
                if (!empty($card->nmID)) {
                    $cards[$order->rid] = $card;
                    $prices[$order->rid] = $order->convertedPrice / 100;
                    $mp[$order->rid] = 1;
                }
            }
        }
        return view('Shop/ordersale', [
            'all' => count($cards),
            'cards' => $cards,
            'prices' => $prices,
            'mp' => $mp,
            'title' => $type == self::ORDER ? 'Заказано' : 'Выкуплено'
        ]);
    }

    public function ordered($date = null)
    {
        return $this->ordersale(self::ORDER, $date);
    }

    public function saled($date = null)
    {
        return $this->ordersale(self::SALE, $date);
    }

    private function fillBartender($order)
    {
        $bartender = new Bartender();
        $bartender->sku = $order->article;
        $bartender->name = $order->card->title;
        $bartender->barcode = $order->skus;
        $bartender->qrcode = $order->qrcode;
        $bartender->partA = $order->partA;
        $bartender->partB = $order->partB;
        $bartender->orderId = $order->orderId;
        $bartender->save();
    }

    public function orderComplete(Request $request)
    {
        $order = MarketplaceOrder::where("orderId", $request->orderId)->first();
        $order->status = 1;
        $order->save();
        $seller = Seller::find($order->seller_id);
        $shipment = MPOrders::getShipment($seller);
        WBMarketplace::addOrderToShipment($seller, $order->orderId, $shipment['id']);
        if ($order->card->slstock) {
            if ($order->card->slstock->is_local) {
                $order->card->slstock->amount = $order->card->slstock->amount - 1;
                if ($order->card->slstock->amount == 0) {
                    $order->card->slstock->is_local = 0;
                    $order->card->slstock->address = 'supplier';
                    if ($order->card->supplier == 10) {
                        $seller = Seller::find($order->card->seller_id);
                        $order->card->removeByStock = 0;
                        $order->card->save();
                        $order->card->prices->percent = 1;
                        $order->card->prices->save();
                        CalcPrice::dispatch($seller, $seller->percentageOfMargin);
                        StockUpdate::dispatch($seller);
                    }
                }
                $order->card->slstock->save();
            }
        }
    }

    public function updateWhl(Request $request)
    {
        $card = Card::find($request->cardId);
        $seller = Seller::find($card->seller_id);
        CardLib::updateDimensions($card, $request, $seller, false);
    }

    public function printOrderBarcode(Request $request)
    {
        $order = MarketplaceOrder::where("orderId", $request->orderId)->first();
        $order->printabled = 1;
        $order->save();
        Bartender::truncate();
        $this->fillBartender($order);
    }

    public function printAll(Request $request)
    {
        $orders = MarketplaceOrder::where("shipmentId", $request->shipment)
            ->where("status", 0)
            ->where("printabled", 0)
            ->orderBy('skus')->get();
        Bartender::truncate();
        foreach ($orders as $order) {
            $this->fillBartender($order);
        }
        MarketplaceOrder::where("shipmentId", $request->shipment)
            ->where("status", 0)
            ->where("printabled", 0)
            ->update(['printabled' => 1]);
    }

    public function orders($shipmentId = null)
    {
        $orders = [];
        $seller = Seller::find(session()->get('sellerId'));
        if (empty($shipmentId)) {
            $shipment = WBMarketplace::getOpenSupplies($seller);
        } else {
            $shipment['id'] = $shipmentId;
        }
        if (!empty($shipment['id'])) {
            $orders = MarketplaceOrder::where("shipmentId", $shipment['id'])->where("status", 0)->orderBy('createdAt')->get();
        }
        return view('Shop/orders', [
            'shipmentId' => $shipment['id'],
            'count' => $orders->count(),
            'sum' => $orders->sum('convertedPrice'),
            'orders' => $orders
        ]);
    }

    public function stock($warehouseName, Request $request, $toSale = 0)
    {
        $seller = Seller::find(session()->get('sellerId'));
        session()->put('backUrl', $request->path());
        if ($warehouseName == 'товаров, которые не участвуют в распродаже') {
            $stocks = Wbststock::where("quantity", '>', 0)
                ->join('cards', 'wbststocks.nmId', '=', 'cards.nmID')
                ->where("wbststocks.seller_id", $seller->id)
                ->where('removeByStock', 0)
                ->offset(0)
                ->limit(100)
                ->get();
        } else {
            if ($toSale) {
                $stocks = Wbststock::where("warehouseName", $warehouseName)
                    ->join('prices', 'wbststocks.nmId', '=', 'prices.nmId')
                    ->where("quantity", ">", 0)
                    ->where("wbststocks.seller_id", $seller->id)
                    ->where('s_price', 0)
                    ->offset(0)
                    ->limit(100)
                    ->get();
            } else {
                $stocks = Wbststock::where("warehouseName", $warehouseName)
                    ->where("quantity", ">", 0)
                    ->where("seller_id", $seller->id)
                    ->offset(0)
                    ->limit(100)
                    ->get();
            }
        }
        $loaded = 1;
        foreach ($stocks as $stock) {
            if (empty($stock->card)) {
                //$loaded = 0;
                ContentCard::dispatch($seller, [
                    'cursor' => [
                        'limit' => 1
                    ],
                    'filter' => [
                        'textSearch' => (string)$stock->nmId,
                        "withPhoto" => -1
                    ]
                ]);
            }
        }
        $saled = [];
        foreach ($stocks as $stock) {
            if (!empty($stock->card->nmID)) {
                $spp = WBContent::spp($seller, $stock->card->nmID);
                if (empty($stock->card->prices)) {
                    PriceInfo::dispatch($seller);
                }
                if(!empty($stock->card->prices)) {
                    $price = floor($stock->card->prices->price * ((100 - $spp) / 100));
                    $diff = $price - $stock->card->prices->s_price;
                } else {
                    $diff = 0;
                }
                if(empty($stock->card->photos[0])) {
                    ContentCard::dispatch($seller, [
                        'cursor' => [
                            'limit' => 10
                        ],
                        'filter' => [
                            'textSearch' => (string)$stock->card->vendorCode,
                            "withPhoto" => -1
                        ]
                    ], 'addPhoto');
                }
                $saled[$stock->id] = true;
                if ($diff < -5 || $diff > 5) {
                    $saled[$stock->id] = false;
                }
            }
        }
        return view('/Shop/stock', [
            'warehouseName' => $warehouseName,
            'stocks' => $stocks,
            'loaded' => $loaded,
            'toSalle' => $toSale,
            'saled' => $saled
        ]);
    }

    public function stocks(Request $request)
    {
        session()->put('backUrl', $request->path());
        $stocksAll = Wbststock::where("seller_id", session()->get('sellerId'))->count();
        $stocks = Wbststock::select(DB::raw('warehouseName, sum(quantity) as amount'))
            ->where('seller_id', session()->get('sellerId'))
            ->where('quantity', '>', '0')
            ->groupBy('warehouseName')
            ->orderByDesc('amount')
            ->get();
        $result = [];
        foreach ($stocks as $stock) {
            $result[] = [
                'name' => $stock->warehouseName,
                'amount' => $stock->amount,
                'percent' => ceil($stock->amount / ($stocksAll / 100))
            ];
        }
        return view('/Shop/stocks', [
            'result' => $result,
        ]);
    }
}
