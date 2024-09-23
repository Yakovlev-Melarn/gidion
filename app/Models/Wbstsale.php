<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wbstsale extends Model
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
        'forPay',
        'finishedPrice',
        'priceWithDisc',
        'saleID',
        'orderType',
        'sticker',
        'gNumber',
        'srid'
    ];
}
