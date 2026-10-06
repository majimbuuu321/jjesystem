<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePaymentLogs extends Model
{
    //
    protected $table = 'invoice_payment_logs';
    protected $fillable = [
        'invoice_header_id',
        'payment_amount',
        'created_by',
        'updated_by',
    ];
}
