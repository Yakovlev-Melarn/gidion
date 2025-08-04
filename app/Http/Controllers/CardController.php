<?php

namespace App\Http\Controllers;

use App\Http\Libs\CardLib;
use App\Http\Libs\Helper;
use App\Http\Libs\OfficeBurg;
use App\Http\Libs\SamsonLib;
use App\Http\Libs\WBContent;
use App\Http\Libs\WBSupplier;
use App\Jobs\CatalogJob;
use App\Jobs\ContentCard;
use App\Jobs\CopyCards;
use App\Jobs\PriceInfo;
use App\Jobs\Trash;
use App\Jobs\UploadImages;
use App\Models\Bartender;
use App\Models\Card;
use App\Models\cardRule;
use App\Models\Catalog;
use App\Models\Comission;
use App\Models\Competitor;
use App\Models\Job;
use App\Models\Seller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    public function catalog(Request $request)
    {
        if ($request->method() == 'POST') {
            CatalogJob::dispatch()->onQueue('samson');
        }
        $products = Catalog::whereNull('card_id')->where('blocked', 0)->limit(20)->inRandomOrder()->get();
        $rows = [];
        foreach ($products as $product) {
            $amount = Helper::arrSearch(SamsonLib::getProductStock($product->sku), 'type', 'idp') ?: 0;
            $obPrice = Helper::arrSearch(SamsonLib::getProductPrice($product->sku), 'type', 'contract') ?: 0;
            if ($amount > 5) {
                if ($obPrice) {
                    $wbPrice = '-';
                    if ($card = Card::where('origSku', $product->sku)->where('supplier', 10)->first()) {
                        if ($card->seller_id != session()->get('sellerId')) {
                            continue;
                        }
                        $card->prices->s_price = WBSupplier::getPrice($card->supplierSku);
                        $card->prices->save();
                        $wbPrice = $card->prices->s_price;
                        $product->card_id = $card->id;
                        $product->save();
                    }
                    $width = Helper::arrSearch(json_decode($product->package_size, 1), 'type', 'width');
                    $height = Helper::arrSearch(json_decode($product->package_size, 1), 'type', 'height');
                    $depth = Helper::arrSearch(json_decode($product->package_size, 1), 'type', 'depth');
                    $volume = (floatval($width) * floatval($height) * floatval($depth)) / 1000;
                    if ($volume > 0) {
                        $rows[] = [
                            'id' => $product->id,
                            'cardId' => $product->card_id,
                            'sku' => $product->sku,
                            'name' => mb_substr($product->name, 0, 50),
                            'omPrice' => $obPrice,
                            'obPrice' => $obPrice,
                            'wbPrice' => $wbPrice,
                            'volume' => ceil($volume)
                        ];
                    } else {
                        $product->blocked = 2;
                        $product->save();
                    }
                } else {
                    $product->blocked = 3;
                    $product->save();
                }
            } else {
                $product->blocked = 4;
                $product->save();
            }
        }
        return view('Cards/catalog', [
            'suppliers' => Supplier::where('id', '!=', 9)->get(),
            'rows' => $rows
        ]);
    }

    public function copyCard(Request $request)
    {
        if ($request->method() == 'POST') {
            if ((int)$request->nmID) {
                $seller = Seller::find(session()->get('sellerId'));
                CopyCards::copyOneCard($request->nmID, $seller, $request->prefix, $request->pack, $request->sku, $request->price);
            }
        }
        return view('Cards/copycard');
    }

    public function deleteCards(Request $request)
    {
        if ($request->method() == 'POST') {
            $seller = Seller::find(session()->get('sellerId'));
            $nmIds = explode("\r\n", $request->nmIds);
            foreach ($nmIds as $nmId) {
                Card::where('nmID', $nmId)->delete();
            }
            $nmIds = array_map('intval', $nmIds);
            $parts = array_chunk($nmIds, 99);
            foreach ($parts as $part) {
                Trash::dispatch($seller, $part);
            }
        }
        return view('Cards/delete');
    }

    public function updateCardInfo($id, Request $request)
    {
        if ($card = Card::find($id)) {
            return $this->{$request->action}($card, $request);
        }
        return ['error' => "card id {$id} not found"];
    }

    protected function update(Card $card, Request $request)
    {
        $seller = Seller::find($card->seller_id);
        CardLib::update($card, $request, $seller);
        return redirect()->back();
    }

    protected function getSamsonPrice(Card $card)
    {
        $card->supplierSku = $card->origSku;
        $prices = SamsonLib::getProductPrice($card->supplierSku);
        if (!empty($prices['error'])) {
            $prices = Catalog::where("sku", $card->supplierSku)->first();
            $card->prices->s_price = ceil(Helper::arrSearch(json_decode($prices->price_list, 1), 'type', 'contract'));
        } else {
            $card->prices->s_price = ceil(Helper::arrSearch($prices, 'type', 'contract'));
        }
        $card->prices->save();
        return $card;
    }

    protected function updateSupplier(Card $card, Request $request)
    {
        $card->supplier = $request->supplier;
        if ($request->supplier == 2) {
            $card = $this->getSamsonPrice($card);
        }
        if ($card->save()) {
            return ['success' => 1];
        }
        return ['error' => 'не удалось сохранить данные о поставщике'];
    }

    public function printBarcode(Request $request)
    {
        Bartender::truncate();
        for ($i = 0; $i < $request->amount; $i++) {
            $bartender = new Bartender();
            $bartender->sku = $request->sku;
            $bartender->name = $request->name;
            $bartender->barcode = $request->barcode;
            $bartender->save();
        }
    }

    public function trash(Request $request)
    {
        $seller = Seller::find(session()->get('sellerId'));
        WBContent::trash($seller, [(int)$request->nmId]);
    }

    public function getCardInfo($id)
    {
        if (!empty($id) && $card = Card::find($id)) {
            $seller = Seller::find($card->seller_id);
            $card = CardLib::promiseSupplier($card);
            $card = CardLib::promiseSku($card);
            $suppliers = Supplier::where('user_id', session()->get('auth'))->get();
            $stocks = $card->stocks;
            $costOfLogistics = CardLib::getDeliveryPrice($card);
            if (empty($card->sizes->count())) {
                Bus::chain([
                    new ContentCard($seller, [
                        'cursor' => [
                            'limit' => 10
                        ],
                        'filter' => [
                            'textSearch' => (string)$card->vendorCode,
                            "withPhoto" => -1
                        ]
                    ], 'addSize'),
                    new PriceInfo($seller, 0, $card->nmID)
                ])->dispatch();
                return view("Card/cardInfo", ['loaded' => 0]);
            }
            if (empty($card->photos->count())) {
                Bus::chain([
                    new ContentCard($seller, [
                        'cursor' => [
                            'limit' => 10
                        ],
                        'filter' => [
                            'textSearch' => (string)$card->vendorCode,
                            "withPhoto" => -1
                        ]
                    ], 'addPhoto'),
                    new PriceInfo($seller, 0, $card->nmID)
                ])->dispatch();
                return view("Card/cardInfo", ['loaded' => 0]);
            }
            if (empty($card->prices)) {
                PriceInfo::dispatch($seller, 0, $card->nmID);
                return view("Card/cardInfo", ['loaded' => 0]);
            }
            $localStock = CardLib::getLocalStock($card->id, session()->get('sellerId'));
            if ($card->supplier == 10) {
                if ($sPrice = WBSupplier::getPrice($card->supplierSku)) {
                    $card->prices->s_price = $sPrice;
                    $card->prices->save();
                }
                if (!$localStock['amount']) {
                    $card->slstock->amount = WBSupplier::getAmount($card->supplierSku);
                    $card->slstock->save();
                }
            }
            if ($card->supplier == 2) {
                $card = $this->getSamsonPrice($card);
            }
            return view("Card/cardInfo", [
                'loaded' => 1,
                'costOfLogistics' => ceil($costOfLogistics),
                'comissionPercent' => $card->comission->comission + CardLib::$tariff,
                'comission' => ceil(($card->prices->price - ($card->prices->price * ($card->prices->discount / 100))) * (($card->comission->comission + CardLib::$tariff) / 100)),
                'realPrice' => ceil($card->prices->price - ($card->prices->price * ($card->prices->discount / 100))),
                'card' => $card,
                'volumetricWeight' => ceil(($card->dimensions->width * $card->dimensions->height * $card->dimensions->length) / 1000),
                'suppliers' => $suppliers,
                'backUrl' => session()->get('backUrl'),
                'seller' => $seller,
                'stocks' => $stocks,
                'localStock' => $localStock,
                'spp' => WBContent::spp($seller, $card->nmID)
            ]);
        }
        return redirect()->back();
    }

    public function comission(Request $request)
    {
        if ($comission = Comission::where('subject', $request->subject)->first()) {
            return $comission->comission;
        }
        return 0;
    }

    public function getRules(Request $request)
    {
        $rules = cardRule::where("subject_id", $request->subjectId)->get();
        return response()->json($rules->toArray());
    }

    public function uploadCard(Request $request)
    {
        $cardData = $request->cardData;
        $cardData['card'] = json_decode($cardData['card'], 1);
        $seller = Seller::find($request->seller);
        $result = WBContent::create($seller, $cardData);
        if (!empty($result['error'])) {
            dd($result);
        }
        $cardData['vendorCode'] = $request->vendorCode;
        Bus::chain([
            new ContentCard($seller, [
                'cursor' => [
                    'limit' => 1
                ],
                'filter' => [
                    'textSearch' => (string)$cardData['vendorCode'],
                    "withPhoto" => -1
                ]
            ]),
            new UploadImages($seller, $cardData['photos'], $cardData['vendorCode']),
        ])->delay(now()->addMinutes(3))->dispatch();
        $product = Catalog::where('sku', $request->sku)->first();
        $product->card_id = 1;
        $product->save();
        return $cardData;
    }

    public function saveRule(Request $request)
    {
        foreach ($request->rules as $rule) {
            $cardRule = new cardRule();
            $cardRule->subject_id = $request->subjectId;
            $cardRule->facet = $request->facet;
            $cardRule->filed = $rule;
            $cardRule->save();
        }
    }

    public function getCharc($subjectId, Request $request)
    {
        $seller = Seller::find($request->seller);
        return WBContent::objectCharc($seller, $subjectId);
    }

    public function getObjectsAll(Request $request)
    {
        $seller = Seller::find($request->seller);
        return WBContent::objectsAll2($seller, $request->name);
    }

    public function createcard($productId)
    {
        $product = Catalog::find($productId);
        return view('Cards/createcard', [
            'product' => $product,
            'sPrice' => Helper::arrSearch(SamsonLib::getProductPrice($product->sku), 'type', 'contract'),
            'photos' => json_decode($product->photo_list),
            'facets' => json_decode($product->facet_list, 1)
        ]);
    }

    private function getAmountsName($filter, $amountNames)
    {
        $amountName = 'С любым остатком';
        if ($filter['amount'] > 0) {
            foreach ($amountNames as $key => $name) {
                if ($key == $filter['amount']) {
                    $amountName = $name;
                }
            }
        }
        return $amountName;
    }

    private function getSupplierName($filter, $suppliers)
    {
        $supplierName = 'Все поставщики';
        if ($filter['supplier'] > 0) {
            foreach ($suppliers as $supplier) {
                if ($supplier->supplierId == $filter['supplier']) {
                    $supplierName = $supplier->name;
                }
            }
        }
        return $supplierName;
    }

    public function changeFilter(Request $request)
    {
        if (isset($request->supplier)) {
            session()->put('fSupplier', $request->supplier);
        }
        if (isset($request->amount)) {
            session()->put('fAmount', $request->amount);
        }
    }

    public function getList($page)
    {
        $amountNames = [
            1 => 'Товары с остатком',
            2 => 'Товары без остатка'
        ];
        $filter = [
            'supplier' => session()->get('fSupplier'),
            'amount' => session()->get('fAmount')
        ];
        $suppliers = Supplier::where("user_id", session()->get('auth'))->orderBy('name')->get();
        $supplierName = $this->getSupplierName($filter, $suppliers);
        $amountName = $this->getAmountsName($filter, $amountNames);
        $offset = ($page - 1) * 100;
        $previousPage = $page - 1;
        $nextPage = $page + 1;
        $cards = DB::table('cards')
            ->leftJoin('stocks', 'cards.id', '=', 'stocks.card_id')
            ->leftJoin('wbststocks', 'cards.nmID', '=', 'wbststocks.nmId')
            ->select('cards.*')
            ->where('cards.syncStatus', 1)
            ->where('cards.seller_id', session()->get('sellerId'))
            ->where('cards.supplier', '>=', '0');
        if (!empty($filter['supplier'])) {
            $cards = $cards->where("supplier", $filter['supplier']);
        }
        if (!empty($filter['amount'])) {
            if ($filter['amount'] == 1) {
                $cards = $cards->where(function ($query) {
                    $query->where('stocks.amount', '>', '0')
                        ->orWhere('wbststocks.quantity', '>', '0');
                });
            } else {
                $cards = $cards->where(function ($query) {
                    $query->whereNull('stocks.amount')->orWhere('stocks.amount', '0');
                })->where(function ($query) {
                    $query->whereNull('wbststocks.quantity')->orWhere('wbststocks.quantity', 0);
                });
            }
        }
        $cards = $cards->groupBy('cards.id');
        $all = $cards->get();
        $all = $all->count();
        $cards = $cards->limit(100)
            ->offset($offset)
            ->orderByDesc("id")
            ->get();
        if ($cards->count() > 0) {
            $resultCards = [];
            foreach ($cards as $card) {
                $resultCards[] = Card::find($card->id);
            }
            $cards = $resultCards;
        }
        $totalPages = ceil($all / 100);
        if ($nextPage > $totalPages) {
            $nextPage = 0;
        }
        return view("Cards/list", [
            'all' => $all,
            'totalPages' => $totalPages,
            'cards' => $cards,
            'page' => $page,
            'previousPage' => $previousPage,
            'nextPage' => $nextPage,
            'suppliers' => $suppliers,
            'supplierName' => $supplierName,
            'amountNames' => $amountNames,
            'amountName' => $amountName,
            'filter' => $filter
        ]);
    }

    public function copy(Request $request)
    {
        $load = 0;
        $seller = Seller::find(session()->get('sellerId'));
        $competitors = Competitor::where("user_id", session()->get('auth'))->get();
        if (Job::where("queue", 'copycards')->count() > 0) {
            $load = 1;
        }
        if ($request->method() == 'POST') {
            CopyCards::dispatch($seller, $request->competitor, $request->count)->onQueue('copycards');
            /*Bus::chain([
                    new PriceInfo($seller),
                    new CalcPrice($seller, $seller->percentageOfMargin),
                    new PriceUpdate($seller),
                    new StockUpdate($seller)
                ]
            )->delay(now()->addMinutes(30))->dispatch();*/
            $load = 1;
        }
        return view('Cards/copy', [
            'competitors' => $competitors,
            'load' => $load
        ]);
    }

    public function getSellStockPrice($id)
    {
        return response()->json(CardLib::getSellStockPrice($id));
    }
}
