<?php

namespace App\Console\Commands;

use App\Jobs\CalcPrice;
use App\Jobs\ContentCard;
use App\Jobs\PriceInfo;
use App\Jobs\StatisticsOrders;
use App\Jobs\StatisticsSales;
use App\Jobs\StatisticsStocks;
use App\Jobs\StockUpdate;
use App\Jobs\Trash;
use App\Models\Card;
use App\Models\Catalog;
use App\Models\Seller;
use App\Models\StateSuppliers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Statistics extends Command
{
    protected $signature = 'statistics:get {method} {nmId?} {sellerId?}';
    protected $description = 'Command description';
    private $nmId;
    private $sellerId;

    public function handle()
    {
        $this->nmId = $this->argument('nmId');
        $this->sellerId = $this->argument('sellerId');
        $method = $this->argument('method');
        $this->$method();
    }

    protected function orders()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            StatisticsOrders::dispatch($seller);
        }
    }

    protected function prices()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            $cards = Card::where("seller_id", $seller->id)->where("supplier", 10)->get();
            foreach ($cards as $card) {
                if (empty($card->prices)) {
                    PriceInfo::dispatch($seller, 0, $card->nmID);
                }
            }
        }
    }

    protected function stocks()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            StatisticsStocks::dispatch($seller);
        }
    }

    protected function sales()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            StatisticsSales::dispatch($seller);
        }
    }

    protected function downloadCard()
    {
        if ($seller = Seller::find($this->sellerId)) {
            ContentCard::dispatch($seller, [
                'cursor' => [
                    'limit' => 1
                ],
                'filter' => [
                    'textSearch' => (string)$this->nmId,
                    "withPhoto" => -1
                ]
            ]);
        }
    }

    protected function calcprice()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            CalcPrice::dispatch($seller, $seller->percentageOfMargin);
        }
    }

    protected function updateStocks()
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            StockUpdate::dispatch($seller)->onQueue('updatestock');
        }
    }

    protected function test()
    {
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
        $url = 'https://api.samsonopt.ru/v1/sku?api_key=635dba79ee799d90ce09e82e1f659c0f&pagination_count=2&photo_size=xl';
        $page = 1;
        $result = Http::withHeader('User-Agent', $agent)->get("{$url}&page={$page}");
        $result = $result->json();
        foreach ($result['data'] as $item) {
            if (!$catalog = Catalog::where('sku', $item['sku'])->first()) {
                $catalog = new Catalog();
                $catalog->sku = $item['sku'];
                $catalog->name = $item['name'];
                $catalog->manufacturer = $item['manufacturer'];
                $catalog->brand = $item['brand'];
                $catalog->barcode = $item['barcode'];
                $catalog->description = $item['description'];
                $catalog->ban_not_multiple = $item['ban_not_multiple'];
                $catalog->out_of_stock = $item['out_of_stock'];
                $catalog->characteristic_list = json_encode($item['characteristic_list'], JSON_UNESCAPED_UNICODE);
                $catalog->facet_list = json_encode($item['facet_list'], JSON_UNESCAPED_UNICODE);
                $catalog->photo_list = json_encode($item['photo_list'], JSON_UNESCAPED_UNICODE);
                $catalog->package_size = json_encode($item['package_size'], JSON_UNESCAPED_UNICODE);
                $catalog->price_list = json_encode($item['price_list'], JSON_UNESCAPED_UNICODE);
                $catalog->stock_list = json_encode($item['stock_list'], JSON_UNESCAPED_UNICODE);
                $catalog->save();
            }
        }
        dd($result);
        /*$url = "https://office-burg.ru/search/?q=150649";
        $response = Http::withHeaders([])->withCookies(['PHPSESSID=pamnt36p24j3off3o6e8kp9q4l; _ym_uid=1714106427158212690; _ym_d=1714106427; BX_USER_ID=5d5f4fcb0972ba605ed2af43604616a2; OP_SAMSON_OP_DEALER_UUID=130d3443-68e0-53b5-9a72-98e83e256e89; OP_SAMSON_OP_DEALER_ID=9087558; OP_SAMSON_CITY_UUID=4e609969-e8e0-513c-a4c0-17cd878c4a65; OP_SAMSON_SALE_UID=1161289077; OP_SAMSON_LOGIN=melarn4u%40gmail.com; OP_SAMSON_SOUND_LOGIN_PLAYED=Y; warning=true; OP_SAMSON_BANNERS=0_16150_1_03052024%2C0_17602_1_03052024%2C0_17576_2_08052024%2C0_16635_1_03052024%2C0_15810_1_03052024%2C0_17609_2_08052024%2C0_17567_2_08052024%2C0_17129_1_03052024%2C0_17410_2_08052024%2C0_17673_2_08052024%2C0_17386_1_03052024%2C0_17668_1_03052024%2C0_17638_1_03052024%2C0_17457_1_08052024%2C0_17462_1_08052024%2C0_17400_1_08052024%2C0_16728_1_08052024; _gid=GA1.2.1996601539.1714543901; _ym_isad=2; WRAP_GA_COUNT=NaN; _ym_visorc=w; OP_SAMSON_WATCHED_GOODS=a%3A16%3A%7Bi%3A1714109533%3Bs%3A6%3A%22143300%22%3Bi%3A1714107279%3Bs%3A6%3A%22144122%22%3Bi%3A1714108559%3Bs%3A6%3A%22150650%22%3Bi%3A1714554342%3Bs%3A6%3A%22191743%22%3Bi%3A1714554348%3Bs%3A6%3A%22191744%22%3Bi%3A1714554349%3Bs%3A6%3A%22191745%22%3Bi%3A1714554354%3Bs%3A6%3A%22191746%22%3Bi%3A1714135613%3Bs%3A6%3A%22201045%22%3Bi%3A1714195204%3Bs%3A6%3A%22270741%22%3Bi%3A1714195240%3Bs%3A6%3A%22270827%22%3Bi%3A1714543912%3Bs%3A6%3A%22291294%22%3Bi%3A1714195317%3Bs%3A6%3A%22361175%22%3Bi%3A1714195357%3Bs%3A6%3A%22404624%22%3Bi%3A1714131142%3Bs%3A6%3A%22510117%22%3Bi%3A1714131179%3Bs%3A6%3A%22605392%22%3Bi%3A1714554357%3Bs%3A6%3A%22191747%22%3B%7D; _ga=GA1.1.1695913439.1714106427; _ga_SRSW4MH0TF=GS1.1.1714553928.9.1.1714554434.0.0.0'], 'office-burg.ru')->get($url);
        $body = $response->body();
        $body = iconv('windows-1251', 'utf-8', $body);
        $dom = new Dom();
        $options = new Options();
        $dom->loadStr($body, $options->setEnforceEncoding('UTF-8'));
        $contents = $dom->find('.Product__buy');
        $dom->loadStr($contents);
        $contents = $dom->find('.Product__remote');
        $result = 0;
        foreach ($contents as $content) {
            $result = filter_var($content, FILTER_SANITIZE_NUMBER_INT);
            $result = filter_var($result, FILTER_VALIDATE_INT);
            break;
        }
        dd($result);*/
    }

    protected function sellerstate()
    {
        $f = 40001;
        $t = 50000;
        $bar = $this->output->createProgressBar($t - $f);
        $bar->start();
        for ($i = $f; $i <= $t; $i++) {
            $response = Http::withHeaders([])
                ->timeout(180)
                ->connectTimeout(180)
                ->acceptJson()
                ->get("https://catalog.wb.ru/sellers/v2/catalog?ab_testing=false&appType=1&curr=rub&dest=-1412209&hide_dtype=10&lang=ru&sort=popular&spp=30&supplier={$i}&uclusters=8");
            $response = $response->json();
            if (!empty($response['data'])) {
                if ($response['data']['total'] > 0) {
                    $product = $response['data']['products'][0];
                    $name = $product['supplier'];
                    $total = $response['data']['total'];
                    $stateSupplier = new StateSuppliers();
                    $stateSupplier->name = $name;
                    $stateSupplier->supplierId = $i;
                    $stateSupplier->productsCount = $total;
                    $stateSupplier->save();
                }
            }
            $bar->advance();
        }
        $bar->finish();
    }

    protected function deloldcards()
    {
        $seller = Seller::find(14);
        $cards = Card::where("seller_id", '=', $seller->id)
            ->where('supplier', '=', 10)
            ->where('createdAt', '<', now()->subMonth()->toDateString());
        if (!empty($this->nmId)) {
            $cards = $cards->where('id', '>', $this->nmId);
        }
        //dd($cards->toRawSql());
        $cards = $cards->limit(1000)->get();
        $nmIds = [];
        $ids = [];
        $bar = $this->output->createProgressBar(count($cards));
        $bar->start();
        $lastId = 0;
        foreach ($cards as $card) {
            /*if (MarketplaceOrder::where("nmId", '=', $card->nmID)
                ->where('createdAt', '>', now()->subMonth()->toDateString())->first()) {
                $lastId = $card->id;
                continue;
            }
            if (Wbstorder::where("nmId", '=', $card->nmID)
                ->where('lastChangeDate', '>', now()->subMonth()->toDateString())->first()) {
                $lastId = $card->id;
                continue;
            }
            if ($card->slstock) {
                if ($card->slstock->is_local) {
                    $lastId = $card->id;
                    continue;
                }
            }*/
            $nmIds[] = $card->nmID;
            $ids[] = $card->nmID;
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info("К удалению добавлено " . count($nmIds) . " шт.");
        if (count($nmIds) > 0) {
            Trash::dispatch($seller, $nmIds);
            $noDelete = [];
            if (!$deleteAllFindedCards = $this->confirm("Удалось удалить все карточки?", true)) {
                $noDelete = $this->ask("Какие nmId через запятую не удалось удалить?");
                $noDelete = explode(',', $noDelete);
            }
            $bar = $this->output->createProgressBar(count($ids));
            foreach ($ids as $id) {
                if (in_array($id, $noDelete)) {
                    continue;
                }
                Card::where("nmID", '=', $id)->delete();
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info($lastId);
            $this->newLine();
        }
    }
}
