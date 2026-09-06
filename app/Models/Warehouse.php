<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    //
     protected $table = 'warehouse';
    protected $fillable = [
        'employee_id',
        'warehouse_name',
        'warehouse_address',
        'warehouse_type_id',
        'route_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function route(){
        return $this->belongsTo(Routes::class, 'route_id', 'id');
    }
}
