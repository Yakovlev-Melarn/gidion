<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function getChartData(Request $request): array
    {
        //$sellerId = $request->json('sellerId');
        $date = $request->json('date');
        if (empty($date)) {
            $date = Carbon::today()->toDateTimeString();
        }
        $nextDay = Carbon::createFromFormat("Y-m-d H:i:s", $date);
        $nextDay = $nextDay->timestamp == Carbon::today()->timestamp ? false : $nextDay->addDay()->toDateTimeString();
        $prevDay = Carbon::createFromFormat("Y-m-d H:i:s", $date);
        $result = [
            'meta' => [
                'nextDay' => $nextDay,
                'selectedDay' => $date,
                'prevDay' => $prevDay->addDays(-1)->toDateTimeString()
            ],
            'data' => [[
                'name' => "00:00",
                'uv' => 0,
                'pv' => 0
            ]]
        ];
        return $result;
    }
}
