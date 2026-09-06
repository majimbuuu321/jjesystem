<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
     protected $table = 'suppliers';
    protected $fillable = [
        'company_name',
        'first_name',
        'middle_name',
        'last_name',
        'supplier_address',
        'email',
        'contact_number',
        'status',
        'created_by',
        'updated_by',
    ];
}
