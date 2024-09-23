<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    public function card()
    {
        return $this->hasOne(Card::class, 'id', 'card_id');
    }
}
