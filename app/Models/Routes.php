<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routes extends Model
{
    //
     protected $table = 'routes';

     protected $fillable = [
        'employee_id',
        'route_name',
        'route_group_id',
        'status',
        'created_by',
        'updated_by',
    ];

     public function route_group(){
        return $this->belongsTo(RouteGroup::class, 'route_group_id', 'id');
    }

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
