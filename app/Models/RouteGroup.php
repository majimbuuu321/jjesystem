<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteGroup extends Model
{
    //
     protected $table = 'route_group';

    protected $fillable = [
        'route_group_name',
        'status',
        'created_by',
        'updated_by',
    ];
}
