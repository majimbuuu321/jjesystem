<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceLog extends Model
{
    //
    protected $table = 'invoice_log';
    protected $fillable = [
        'invoice_header_id',
        'invoice_log',
        'log_type',
        'created_by',
        'updated_by',
    ];
}
