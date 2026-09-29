<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class PurchaseOrderHeader extends Model
{
    //
    protected $table = 'purchase_order_header';
    protected $fillable = [
        'purchase_order_no',
        'invoice_no',
        'received_date',
        'supplier_id',
        'warehouse_id',
        'payment_terms_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function paymentTerms(){
        return $this->belongsTo(PaymentTerms::class, 'payment_terms_id', 'id');
    }

    public function PurchaseOrderDetail(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

}
