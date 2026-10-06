<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryPerWarehouse extends Model
{
    //
    public $timestamps = false;
    protected $table = 'inventory_per_warehouse';
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'unit_code',
        'quantity',
        'updated_at',
    ];

    
}
