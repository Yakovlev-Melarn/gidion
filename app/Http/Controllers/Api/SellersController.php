<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReactSession;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellersController extends Controller
{
    public function getSellersList()
    {
        $sellers = Seller::where('user_id', session()->get('auth'))->get();
        foreach ($sellers as $seller) {
            $seller->selected = false;
            if (session()->get('seller') == $seller->id) {
                $seller->selected = true;
            }
        }
        return response()->json($sellers);
    }

    public function setSelectedSeller(Request $request)
    {
        $sellerId = $request->json('sellerId');
        if ($sellerSession = ReactSession::where("userId", session('auth'))->where("key", "seller")->first()) {
            $sellerSession->value = $sellerId;
            $sellerSession->save();
        }
        return $this->getSelectedSeller();
    }

    public function getSelectedSeller()
    {
        if (!$sellerSession = ReactSession::where("userId", session('auth'))->where("key", "seller")->first()) {
            $seller = Seller::where('user_id', session('auth'))->first();
            $sellerSession = new ReactSession();
            $sellerSession->userId = session('auth');
            $sellerSession->key = 'seller';
            $sellerSession->value = $seller->id;
            $sellerSession->save();
        } else {
            $seller = Seller::find($sellerSession->value);
        }
        return response()->json([
            'id' => $seller->id,
            'name' => $seller->name,
            'selected' => ReactSession::where("userId", session('auth'))->get()->toArray()
        ]);
    }
}
