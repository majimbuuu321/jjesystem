<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovements extends Model
{
    //
    public $timestamps = false;
    protected $table = 'stock_movements';
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'supplier_id',
        'unit_code',
        'movement_type',
        'quantity',
        'reference_note',
        'module',
        'status',
        'created_by',
        'created_at',
    ];
}
