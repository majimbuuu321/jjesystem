<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    //
    public $timestamps = false;
    protected $table = 'invoice_detail';
    protected $fillable = [
        'invoice_header_id',
        'price_code_id',
        'products_id',
        'uom_id',
        'quantity',
        'price',
        'gross_amount',
        'discount_rate',
        'discount_amount',
        'net_amount',
        'tag_weight',
        'net_weight',
        'remarks',
    ];

     public function priceCode(){
        return $this->belongsTo(PriceCode::class, 'price_code_id', 'id');
    }
    public function product(){
        return $this->belongsTo(Products::class, 'products_id', 'id');
    }
}
