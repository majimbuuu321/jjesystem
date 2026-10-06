<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceType extends Model
{
    //
    protected $table = 'invoice_type';
    protected $fillable = [
        'invoice_type_code',
        'invoice_type_name',
        'status',
        'created_by',
        'updated_by',
    ];
}
