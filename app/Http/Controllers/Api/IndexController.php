<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function getChartData(Request $request): array
    {
        $sellerId = $request->json('sellerId');
        $date = $request->json('date');
        if (empty($date)) {
            $date = Carbon::today()->toDateTimeString();
        }
        $result = [
            'meta' => [
                'nextDay' => Carbon::createFromFormat("Y-m-d H:i:s", $date)->addDay()->toDateTimeString(),
                'selectedDay' => $date,
                'prevDay' => Carbon::createFromFormat("Y-m-d H:i:s", $date)->addDays(-1)->toDateTimeString()
            ],
            'data' => [[
                'name' => '00:00',
                'uv' => 0,
                'pv' => 0
            ]]
        ];
        return $result;
    }
}
