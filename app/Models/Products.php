<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    //
     protected $table = 'products';
    protected $fillable = [
        'price_date',
        'product_category_id',
        'supplier_id',
        'warehouse_id',
        'product_code',
        'product_description',
        'unit_cost',
        'unit_price',
        'reorder_level',
        'weight',
        'status',
        'created_by',
        'updated_by',
    ];
}
