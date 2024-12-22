<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wbstorder;
use App\Models\Wbstsale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    private mixed $date, $nextDay, $prevDay;
    private array $chartTickLabel;
    private int $sellerId;
    private mixed $salesData, $ordersData;

    private function chartInit($sellerId, $date)
    {
        $this->sellerId = (int)$sellerId;
        $this->getChartTickLabel();
        $this->getCarbonDate($date);
        $this->getSales();
        $this->getOrders();
    }

    private function getChartTickLabel()
    {
        for ($i = 0; $i < 24; $i++) {
            $this->chartTickLabel[] = $i < 10 ? "0{$i}" : $i;
        }
    }

    private function getCarbonDate(mixed $date = '')
    {
        $this->date = $date;
        if (empty($this->date)) {
            $this->date = $date = Carbon::today()->toDateTimeString();
        }
        $nextDay = Carbon::createFromFormat("Y-m-d H:i:s", $date);
        $this->nextDay = $nextDay->timestamp == Carbon::today()->timestamp ?
            false : $nextDay->addDay()->toDateTimeString();
        $this->prevDay = Carbon::createFromFormat("Y-m-d H:i:s", $date)->addDays(-1)->toDateTimeString();
    }

    private function getSales()
    {
        $this->salesData = $this->getData(Wbstsale::class);
    }

    private function getOrders()
    {
        $this->ordersData = $this->getData(Wbstorder::class);
    }

    private function getData($type)
    {
        $date = explode(' ', $this->date);
        $result = $type::where('date', 'like', "{$date[0]}%")->where("seller_id", $this->sellerId)->get();
        $data = [];
        foreach ($result as $item) {
            $dhis = explode(' ', $item->date);
            $dhis = explode(':', $dhis[1]);
            isset($data[$dhis[0]]) ? $data[$dhis[0]] += $item->finishedPrice : $data[$dhis[0]] = $item->finishedPrice;
        }
        return $data;
    }

    public function getChartData(Request $request): array
    {
        $this->chartInit($request->json('sellerId'), $request->json('date'));
        $result = [];
        foreach ($this->chartTickLabel as $hour) {
            $result[] = [
                'name' => $hour,
                'pv' => isset($this->salesData[$hour]) ? $this->salesData[$hour] : 0,
                'uv' => isset($this->ordersData[$hour]) ? $this->ordersData[$hour] : 0
            ];
        }
        return [
            'meta' => [
                'nextDay' => $this->nextDay,
                'selectedDay' => $this->date,
                'prevDay' => $this->prevDay
            ],
            'data' => $result
        ];
    }
}
