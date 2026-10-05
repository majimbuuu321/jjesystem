<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryDetail extends Model
{
    //
    public $timestamps = false;
    protected $table = 'inventory_detail';
    protected $fillable = [
        'inventory_header_id',
        'products_id',
        'uom_id',
        'quantity',
        'weight',
        'unit_cost',
        'gross_amount',
        'remarks',
    ];


    public function product(){
        return $this->belongsTo(Products::class, 'products_id', 'id');
    }
    public function unitOfMeasurement(){
        return $this->belongsTo(UnitOfMeasurement::class, 'uom_id', 'id');
    }
}
