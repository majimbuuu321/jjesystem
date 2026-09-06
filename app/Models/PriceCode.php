<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceCode extends Model
{
    //
    protected $table = 'price_code';
    protected $fillable = [
        'price_code',
        'status',
        'created_by',
        'updated_by',
    ];
}
