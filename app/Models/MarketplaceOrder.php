<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceOrder extends Model
{
    use HasFactory;

    public function card()
    {
        return $this->hasOne(Card::class, 'nmID', 'nmId');
    }
    public function size()
    {
        return $this->hasOne(CardSizes::class, 'skus', 'skus');
    }
}
