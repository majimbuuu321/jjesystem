<?php

namespace App\Http\Controllers;

use App\Models\InvoiceHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CustomerReportController extends Controller
{
    //
     public function salesReport(Request $request)
    {
        $dateFrom = Carbon::parse($request->date_from)
            ->startOfDay();

        $dateTo = Carbon::parse($request->date_to)
            ->endOfDay();

        $customers = $request->input('customer', []);

        /*
        |--------------------------------------------------------------------------
        | CASH INVOICES
        |--------------------------------------------------------------------------
        */

        $cashQuery = InvoiceHeader::query()
            ->with([
                'customer',
                'employee',
                'paymentTerms',
                'route',
                'invoiceType',
            ])
            ->whereBetween('invoice_date', [
                $dateFrom,
                $dateTo,
            ])
            ->where('status', 'Paid')
            ->where('invoice_type_id', 2);

        if (!empty($customers)) {
            $cashQuery->whereIn(
                'customer_id',
                $customers
            );
        }

        $cashInvoices = $cashQuery
            ->orderBy('customer_id')
            ->orderBy('invoice_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CHARGE INVOICES
        |--------------------------------------------------------------------------
        */

        $chargeQuery = InvoiceHeader::query()
            ->with([
                'customer',
                'employee',
                'paymentTerms',
                'route',
                'invoiceType',
            ])
            ->whereBetween('invoice_date', [
                $dateFrom,
                $dateTo,
            ])
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->where('invoice_type_id', 1);

        if (!empty($customers)) {
            $chargeQuery->whereIn(
                'customer_id',
                $customers
            );
        }

        $chargeInvoices = $chargeQuery
            ->orderBy('customer_id')
            ->orderBy('invoice_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        $cashTotal = $cashInvoices->sum('paid_amount');

        $chargeTotal = $chargeInvoices->sum('paid_amount');

        $netSale = $cashTotal + $chargeTotal;


        /*
        |--------------------------------------------------------------------------
        | GROUP BY CUSTOMER
        |--------------------------------------------------------------------------
        */

        $cashByCustomer = $cashInvoices->groupBy('customer_id');

        $chargeByCustomer = $chargeInvoices->groupBy('customer_id');


        return view('pdf.sales_per_customer', [
            'cashInvoices' => $cashInvoices,
            'chargeInvoices' => $chargeInvoices,
            'cashTotal' => $cashTotal,
            'chargeTotal' => $chargeTotal,
            'netSale' => $netSale,
            'cashByCustomer' => $cashByCustomer,
            'chargeByCustomer' => $chargeByCustomer,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }
}
