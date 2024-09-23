<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class GetSellersListController extends Controller
{
    public function getList()
    {
        $sellers = Seller::where('user_id', session()->get('auth'))->get();
        return response()->json($sellers);
    }
}
