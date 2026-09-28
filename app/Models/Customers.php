<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    //
    protected $table = 'customers';
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'employee_id',
        'price_code_id',
        'route_id',
        'store_name',
        'street_unit_building_no',
        'region_code',
        'province_code',
        'city_code',
        'brgy_code',
        'contact_number',
        'status',
        'created_by',
        'updated_by',
    ];
}
