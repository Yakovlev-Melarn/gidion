<?php

namespace App\Http\Controllers;

use App\Http\Libs\CardLib;
use App\Models\Card;
use App\Models\CardSizes;
use App\Models\MarketplaceOrder;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    private $requestData;

    public function search(Request $request)
    {
        $this->requestData = $request;
        if ($request->type == 'card') {
            $cards = $this->searchCards();
            return view('Search/result', [
                'cards' => $cards,
                'searchType' => $request->type
            ]);
        }
        if ($request->type == 'order') {
            $orders = $this->searchOrders();
            return view('Search/result', [
                'orders' => $orders,
                'searchType' => $request->type
            ]);
        }
        return view('Search/result', [
            'orders' => [],
            'searchType' => $request->type
        ]);
    }

    private function searchOrders()
    {
        $orders = MarketplaceOrder::where(function ($query) {
            $query->orWhere("orderId", $this->requestData->search)
                ->orWhere("skus", $this->requestData->search)
                ->orWhere("qrcode", $this->requestData->search)
                ->orWhere("article", 'like', "%{$this->requestData->search}%")
                ->orWhere(DB::raw("CONCAT(partA,partB)"), $this->requestData->search);
        })->get();
        if ($orders->count()) {
            foreach ($orders as $order) {
                $seller = Seller::find($order->card->seller_id);
                if (session()->get("sellerId") != $seller->id) {
                    session()->remove('sellerId');
                    session()->remove('sellerName');
                    session()->put('sellerId', $seller->id);
                    session()->put('sellerName', $seller->name);
                }
                CardLib::checkPhotos($order->card);
            }
            $orders = MarketplaceOrder::where(function ($query) {
                $query->orWhere("orderId", $this->requestData->search)
                    ->orWhere("skus", $this->requestData->search)
                    ->orWhere("qrcode", $this->requestData->search)
                    ->orWhere("article", 'like', "%{$this->requestData->search}%")
                    ->orWhere(DB::raw("CONCAT(partA,partB)"), $this->requestData->search);
            })->get();
        }
        return $orders;
    }

    private function searchCards()
    {
        $result = [];
        $barcodes = CardSizes::where("seller_id", session()->get("sellerId"))
            ->where("skus", $this->requestData->search)->get();
        foreach ($barcodes as $barcode) {
            if ($barcode->card) {
                $result[] = $barcode->card;
            }
        }
        if (!count($result)) {
            $result = Card::where(function ($query) {
                    $query->where("nmID", $this->requestData->search)
                        ->orWhere("supplierSku", $this->requestData->search)
                        ->orWhere("origSku", $this->requestData->search)
                        ->orWhere("title", 'like', "%{$this->requestData->search}%")
                        ->orWhere("vendorCode", 'like', "%{$this->requestData->search}%");
                })->limit(100)->get();
        }
        return $result;
    }

}
