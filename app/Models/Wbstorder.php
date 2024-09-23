<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wbstorder extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'lastChangeDate',
        'warehouseName',
        'countryName',
        'oblastOkrugName',
        'regionName',
        'supplierArticle',
        'nmId',
        'barcode',
        'category',
        'subject',
        'brand',
        'techSize',
        'incomeID',
        'isSupply',
        'isRealization',
        'totalPrice',
        'discountPercent',
        'spp',
        'finishedPrice',
        'priceWithDisc',
        'isCancel',
        'cancelDate',
        'orderType',
        'sticker',
        'gNumber',
        'srid'
    ];
}
