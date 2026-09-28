<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    //
    protected $table = 'provinces';
    protected $fillable = [
        'province_name',
        'province_code',
        'region_code',
    ];
}
