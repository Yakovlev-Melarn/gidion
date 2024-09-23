<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wbststock extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'seller_id',
        'lastChangeDate',
        'warehouseName',
        'supplierArticle',
        'nmId',
        'barcode',
        'quantity',
        'inWayToClient',
        'inWayFromClient',
        'quantityFull',
        'category',
        'subject',
        'brand',
        'techSize',
        'Price',
        'Discount',
        'isSupply',
        'isRealization',
        'SCCode'
    ];

    public function card()
    {
        return $this->hasOne(Card::class, 'nmID', 'nmId');
    }
}
