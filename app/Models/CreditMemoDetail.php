<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CreditMemoDetail extends Model
{
    //
    protected $table = 'credit_memo_detail';
    public $timestamps = false;
    protected $fillable = [
        'credit_memo_id',
        'product_id',
        'quantity',
        'weight',
        'unit_cost',
        'total_cost',
        'selling_price',
        'amount',
        'remarks',
    ];

    public function creditMemo(): BelongsTo
    {
        return $this->belongsTo(
            CreditMemoHeader::class,
            'credit_memo_id'
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Products::class,
            'product_id'
        );
    }
}
