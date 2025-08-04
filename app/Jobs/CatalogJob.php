<?php

namespace App\Jobs;

use App\Models\Catalog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CatalogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $uniqueFor = 360000;
    public $timeout = 360000;
    public $url;

    public function __construct($url = null)
    {
        if (empty($url)) {
            $this->url = 'https://api.samsonopt.ru/v1/sku?api_key=c63363e9e46de524234f80de15711aee&photo_size=xl&pagination_count=1000';
        } else {
            $this->url = $url;
        }
    }

    public function handle(): void
    {
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
        $result = Http::withHeader('User-Agent', $agent)->get($this->url);
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
                echo json_encode($catalog->toArray(), 256) . "\r\n";
            }
        }
        if (!empty($result['meta']['pagination']['next'])) {
            echo "\r\n\r\n{$result['meta']['pagination']['next']}\r\n\r\n";
            CatalogJob::dispatch($result['meta']['pagination']['next'])->onQueue('samson');
        }
    }
}
