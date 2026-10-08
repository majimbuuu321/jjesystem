<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InvoicePaymentLogs extends Model
{
    //
    protected $table = 'invoice_payment_logs';
    protected $fillable = [
        'invoice_header_id',
        'payment_date',
        'payment_method',
        'reference_no',
        'payment_amount',
        'created_by',
        'updated_by',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceHeader::class, 'invoice_header_id');
    }
}
