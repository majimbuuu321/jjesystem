<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
class InventoryHeader extends Model
{
    //
    protected $table = 'inventory_header';
    protected $fillable = [
        'document_no',
        'transfer_date',
        'inventory_type_id',
        'transfer_from',
        'transfer_to',
        'assigned_from',
        'assigned_to',
        'plate_no',
        'status',
        'created_by',
        'updated_by',
    ];

    public function inventory_type(){
        return $this->belongsTo(InventoryType::class, 'inventory_type_id', 'id');
    }

    public function supplierFrom(){
        return $this->belongsTo(Supplier::class, 'transfer_from', 'id');
    }

    public function warehouseFrom(){
        return $this->belongsTo(Warehouse::class, 'transfer_from', 'id');
    }

    public function supplierTo(){
        return $this->belongsTo(Supplier::class, 'transfer_to', 'id');
    }

    public function warehouseTo(){
        return $this->belongsTo(Warehouse::class, 'transfer_to', 'id');
    }

    public function InventoryDetail(): HasMany
    {
        return $this->hasMany(InventoryDetail::class);
    }
}
