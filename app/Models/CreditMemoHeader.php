<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CreditMemoHeader extends Model
{
    //
    protected $table = 'credit_memo_header';

    protected $fillable = [
        'credit_memo_date',
        'credit_memo_no',
        'invoice_id',
        'credit_memo_type',
        'warehouse_id',
        'customer_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(
            Warehouse::class,
            'warehouse_id'
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            Customers::class,
            'customer_id'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            CreditMemoDetail::class,
            'credit_memo_id'
        );
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(
            InvoiceHeader::class,
            'invoice_id'
        );
    }
}
