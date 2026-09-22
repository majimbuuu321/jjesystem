<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitPriceHistory extends Model
{
    //
    protected $table = 'unit_price_history';
    protected $fillable = [
        'price_date',
        'product_id',
        'unit_price',
        'created_by',
    ];
}
