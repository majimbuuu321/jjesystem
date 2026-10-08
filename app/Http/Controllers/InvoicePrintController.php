<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceHeader;
class InvoicePrintController extends Controller
{
    //
    public function print(InvoiceHeader $invoice)
    {
        $invoice->load([
            'warehouse',
            'paymentTerms',
            'customer',
            'employee',
            'route',
            'invoiceType',
            'InvoiceDetails.product',
            'InvoiceDetails.priceCode',
            'InvoiceDetails.units',
            'payments',
        ]);

        return view('pdf.invoice', [
            'invoice' => $invoice,
        ]);
    }
}
