<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //
    protected $table = 'cities';
    protected $fillable = [
        'city_name',
        'province_code',
        'city_code',
        'region_code',
    ];
}
