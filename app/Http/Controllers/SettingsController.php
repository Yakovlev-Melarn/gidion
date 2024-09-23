<?php

namespace App\Http\Controllers;

use App\Http\Libs\CardLib;
use App\Http\Libs\WBContent;
use App\Jobs\CalcPrice;
use App\Jobs\DiscountRemove;
use App\Jobs\StockUpdate;
use App\Models\Card;
use App\Models\Competitor;
use App\Models\Job;
use App\Jobs\SyncCardsJob;
use App\Models\MarketplaceOrder;
use App\Models\Price;
use App\Models\Seller;
use App\Models\Supplier;
use App\Models\Wbstorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function changeSeller($id)
    {
        $seller = Seller::find($id);
        session()->remove('sellerId');
        session()->remove('sellerName');
        session()->put('sellerId', $seller->id);
        session()->put('sellerName', $seller->name);
        return response()->redirectTo('/');
    }

    public function updateSeller(Request $request)
    {
        if ($seller = Seller::where("id", $request->id)->where("user_id", session()->get('auth'))->first()) {
            if (!empty($request->key)) {
                $seller->apiKey = $request->key;
            }
            $seller->whID = $request->whID;
            $seller->save();
            return response()->json(['status' => 1]);
        }
        return response()->json(['status' => 0]);
    }

    private function deleteCards(Request $request)
    {
        Card::where("syncStatus", 0)->where("seller_id", session()->get('sellerId'))->delete();
    }

    private function removeDiscount(){
        $prices = Price::where("discount",'>',0)->get();
        foreach ($prices as $price){
            if(!empty($price->card)){
                DiscountRemove::dispatch($price->card->seller, $price->card);
                $price->discount = 0;
                $price->save();
            }
        }
    }
    private function syncCards(Request $request)
    {
        $seller = Seller::find(session()->get('sellerId'));
        $settings = [
            'cursor' => [
                'limit' => 100
            ],
            'filter' => [
                "withPhoto" => -1
            ]
        ];
        Card::where('syncStatus', 1)->where('supplier', '>', '0')->where('seller_id', session()->get('sellerId'))->update(['syncStatus' => 0]);
        SyncCardsJob::dispatch($seller, $settings)->onQueue('synccards');
    }

    private function updatePrice(Request $request)
    {
        $seller = Seller::find(session()->get('sellerId'));
        if ($request->wbpercent) {
            CalcPrice::dispatch($seller, $request->wbpercent)->onQueue('updateprice');
        } else {
            CalcPrice::dispatch($seller, $request->percent)->onQueue('updateprice');
        }
    }

    private function updateStock(Request $request)
    {
        $seller = Seller::find(session()->get('sellerId'));
        StockUpdate::dispatch($seller)->onQueue('updatestock');
    }

    public function runProcess(Request $request)
    {
        $process = $request->process;
        $this->$process($request);
    }

    private function trash(Request $request)
    {
        $offset = 0;
        $seller = Seller::find(session()->get('sellerId'));
        do {
            $nmIds = [];
            $date = now()->subMonth()->toDateTimeString();
            $emptyStocks = DB::table('cards')->select('cards.*')
                ->leftJoin('stocks', 'cards.id', '=', 'stocks.card_id')
                ->whereNotNull('stocks.amount')->where('stocks.amount', '=', 0)
                ->where('stocks.is_local', 0)->whereNotNull('stocks.is_local')
                ->where('cards.supplier', '=', 10)
                ->where('cards.removeByStock', '=', 0)
                ->where('cards.seller_id', '=', $seller->id)
                ->limit(100)->offset($offset)->get();
            if ($emptyStocks->count() > 0) {
                $offset += 100;
                foreach ($emptyStocks as $emptyStock) {
                    if (Wbstorder::where("nmId", $emptyStock->nmID)->where('lastChangeDate', '>', $date)->count() > 0) {
                        continue;
                    }
                    if (MarketplaceOrder::where("nmId", $emptyStock->nmID)->where('createdAt', '>', $date)->count() > 0) {
                        continue;
                    }
                    $nmIds[] = $emptyStock->nmID;
                }
                WBContent::trash($seller, $nmIds);
            }
        } while ($emptyStocks->count() > 0);
    }

    public function process(Request $request)
    {
        $seller = Seller::find(session()->get('sellerId'));
        $processSyncCards = $processUpdatePrice = $processUpdateStock = 0;
        $oldCardsCount = Card::where("syncStatus", 0)->where("seller_id", $seller->id)->count();
        $emptyStock = DB::table('cards')->select('cards.*')
            ->leftJoin('stocks', 'cards.id', '=', 'stocks.card_id')
            ->whereNotNull('stocks.amount')->where('stocks.amount', '=', 0)
            ->where('stocks.is_local', 0)->whereNotNull('stocks.is_local')
            ->where('cards.supplier', '=', 10)
            ->where('cards.removeByStock', '=', 0)
            ->where('cards.seller_id', '=', $seller->id);
        if (Job::where("queue", "synccards")->first()) {
            $processSyncCards = 1;
        }
        if (Job::where("queue", "updateprice")->first()) {
            $processUpdatePrice = 1;
        }
        if (Job::where("queue", "updatestock")->first()) {
            $processUpdateStock = 1;
        }
        return view('Settings/process', [
            'processSyncCards' => $processSyncCards,
            'oldCardsCount' => $oldCardsCount,
            'processUpdatePrice' => $processUpdatePrice,
            'processUpdateStock' => $processUpdateStock,
            'percentageOfMargin' => $seller->percentageOfMargin,
            'emptyStockCount' => $emptyStock->count()
        ]);
    }

    public function sellers(Request $request)
    {
        $sellers = Seller::where("user_id", session()->get("auth"))->get();
        return view("Settings/sellers", ['sellers' => $sellers]);
    }

    public function competitors(Request $request)
    {
        $competitors = Competitor::where("user_id", session()->get("auth"))->get();
        return view("Settings/competitors", ['competitors' => $competitors]);
    }

    public function addCompetitor(Request $request)
    {
        if ($request->isMethod('POST')) {
            $competitor = new Competitor();
            $competitor->user_id = session()->get('auth');
            $competitor->name = $request->name;
            $competitor->url = $request->url;
            $competitor->save();
            return response()->redirectTo('settings/competitors');
        }
        return view('Settings/addcompetitor');
    }

    public function deleteCompetitor(Request $request)
    {
        if ($request->isMethod('POST')) {
            if ($competitor = Competitor::where("id", $request->id)->where("user_id", session()->get('auth'))->first()) {
                $competitor->delete();
                return response()->json(['status' => 1]);
            }
        }
        return response()->json(['status' => 0]);
    }

    public function suppliers(Request $request)
    {
        $suppliers = Supplier::where("user_id", session()->get("auth"))->get();
        return view("Settings/suppliers", ['suppliers' => $suppliers]);
    }

    public function deleteSupplier(Request $request)
    {
        if ($request->isMethod('POST')) {
            if ($supplier = Supplier::where("id", $request->id)->where("user_id", session()->get('auth'))->first()) {
                $supplier->delete();
                return response()->json(['status' => 1]);
            }
        }
        return response()->json(['status' => 0]);
    }

    public function addSupplier(Request $request)
    {
        if ($request->isMethod('POST')) {
            $supplier = new Supplier();
            $supplier->user_id = session()->get('auth');
            $supplier->name = $request->name;
            $supplier->supplierId = $request->supplierId;
            $supplier->url = $request->url;
            $supplier->prefix = $request->prefix;
            $supplier->save();
            return response()->redirectTo('settings/suppliers');
        }
        return view('Settings/addsupplier');
    }

    public function addSeller(Request $request)
    {
        if ($request->isMethod('POST')) {
            $seller = new Seller();
            $seller->user_id = session()->get('auth');
            $seller->name = $request->name;
            $seller->apiKey = $request->token;
            $seller->whID = $request->whID;
            $seller->save();
            session()->put('sellerId', $seller->id);
            session()->put('sellerName', $seller->name);
            return response()->redirectTo('/');
        }
        return view('Settings/addseller');
    }
}
