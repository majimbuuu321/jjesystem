<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryType extends Model
{
    //
    protected $table = 'inventory_type';
    protected $fillable = [
        'inventory_type',
        'inventory_from',
        'inventory_to',
        'status',
        'created_by',
        'updated_by',
    ];
}
