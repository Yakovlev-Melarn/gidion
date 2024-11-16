<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seller;

class InfoController extends Controller
{
    public function getSellersList()
    {
        $sellers = Seller::where('user_id', session()->get('auth'))->get();
        return response()->json($sellers);
    }
}
