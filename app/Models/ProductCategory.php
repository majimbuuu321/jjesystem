<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    //
     protected $table = 'product_category';
    protected $fillable = [
        'product_category_name',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
