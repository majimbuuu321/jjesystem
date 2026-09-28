<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    //
    protected $table = 'barangay';
    protected $fillable = [
        'brgy_code',
        'brgy_name',
        'region_code',
        'province_code',
        'city_code',
    ];
}
