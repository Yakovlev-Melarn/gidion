<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    public function sizes()
    {
        return $this->hasOne(CardSizes::class, 'card_id', 'card_id');
    }

    public function card()
    {
        return $this->hasOne(Card::class, 'id', 'card_id');
    }
}
