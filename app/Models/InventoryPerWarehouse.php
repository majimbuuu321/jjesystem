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


    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
    
}
