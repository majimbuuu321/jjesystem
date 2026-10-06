<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class InvoiceHeader extends Model
{
    //
    protected $table = 'invoice_header';
    protected $fillable = [
        'invoice_type_id',
        'transaction_no',
        'order_no',
        'invoice_date',
        'warehouse_id',
        'customer_id',
        'employee_id',
        'route_id',
        'payment_terms_id',
        'total_amount',
        'balance_amount',
        'status',
        'created_by',
        'updated_by',
    ];

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function paymentTerms(){
        return $this->belongsTo(PaymentTerms::class, 'payment_terms_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function route()
    {
        return $this->belongsTo(Routes::class, 'route_id', 'id');
    }

     public function InvoiceDetails(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function invoiceType()
    {
        return $this->belongsTo(InvoiceType::class, 'invoice_type_id', 'id');
    }
}
