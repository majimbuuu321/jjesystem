<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $table = 'employees';
    protected $fillable = [
        'employee_code',
        'first_name',
        'middle_name',
        'address',
        'last_name',
        'gender',
        'contact_number',
        'birth_date',
        'status',
        'created_by',
        'updated_by',
    ];
}
