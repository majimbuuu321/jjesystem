<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricePerCode extends Model
{
    //
    protected $table = 'price_per_code';
    protected $fillable = [
        'price_date',
        'products_id',
        'units_id',
        'price_code_id',
        'unit_price',
        'created_by',
        'updated_by',
    ];
}
