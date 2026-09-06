<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOfMeasurement extends Model
{
    //
     protected $table = 'units';

    protected $fillable = [
        'unit_code',
        'unit_name',
        'status',
        'created_by',
        'updated_by',
    ];
}
