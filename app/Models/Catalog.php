<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use HasFactory;

    public function catalogcard()
    {
        return $this->hasOne(Card::class, 'id', 'card_id');
    }
}
