<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessChannel extends Model
{
    //
    protected $table = 'business_channel';
    protected $fillable = [
        'business_channel_name',
        'status',
        'created_by',
        'updated_by',
    ];
}
