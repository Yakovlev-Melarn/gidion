<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    public function photos()
    {
        return $this->hasMany(CardPhoto::class, 'card_id', 'id');
    }

    public function dimensions()
    {
        return $this->hasOne(CardDimensions::class, 'card_id', 'id');
    }

    public function cardsupplier()
    {
        return $this->hasOne(Supplier::class, 'supplierId', 'supplier');
    }
    public function cardcatalog()
    {
        return $this->hasOne(Catalog::class, 'sku', 'origSku');
    }
    public function cardcatalogid()
    {
        return $this->hasOne(Catalog::class, 'card_id', 'id');
    }

    public function prices()
    {
        return $this->hasOne(Price::class, 'card_id', 'id');
    }

    public function comission()
    {
        return $this->hasOne(Comission::class, 'subject', 'subjectName');
    }

    public function sizes()
    {
        return $this->hasMany(CardSizes::class, 'card_id', 'id');
    }

    public function slstock()
    {
        return $this->hasOne(Stock::class, 'card_id', 'id');
    }

    public function seller()
    {
        return $this->hasOne(Seller::class, 'id', 'seller_id');
    }

    public function stocks()
    {
        return $this->hasMany(Wbststock::class, 'nmId', 'nmID');
    }
}
