<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    //
    public $timestamps = false;
    protected $table = 'purchase_order_detail';
    protected $fillable = [
        'purchase_order_header_id',
        'price_code_id',
        'products_id',
        'uom_id',
        'quantity',
        'tag_weight',
        'unit_cost',
        'total_cost',
        'discount_rate',
        'discount_amount',
        'net_amount',
        'remarks',
    ];

}
