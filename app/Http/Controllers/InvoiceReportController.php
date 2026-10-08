<?php

namespace App\Http\Controllers;

use App\Models\InvoiceHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvoiceReportController extends Controller
{
    //
    public function salesReport(Request $request)
    {
    //     $dateFrom = Carbon::parse($request->date_from)->startOfDay();
    //     $dateTo   = Carbon::parse($request->date_to)->endOfDay();
    //     $salesRepresentatives = $request->input(
    //     'sales_representative',
    //     []);
    //     /*
    //     |--------------------------------------------------------------------------
    //     | CASH INVOICES
    //     |--------------------------------------------------------------------------
    //     */
    //     $cashInvoices = InvoiceHeader::query()
    //         ->with([
    //             'customer',
    //             'employee',
    //             'paymentTerms',
    //             'invoiceType',
    //         ])
    //         ->whereBetween('invoice_date', [$dateFrom, $dateTo])
    //         ->whereIn('status', ['Paid'])
    //         ->where('invoice_type_id', 2)
    //         ->orderBy('invoice_date')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | CHARGE INVOICES
    //     |--------------------------------------------------------------------------
    //     */
    //     $chargeInvoices = InvoiceHeader::query()
    //         ->with([
    //             'customer',
    //             'employee',
    //             'paymentTerms',
    //             'invoiceType',
    //         ])
    //         ->whereBetween('invoice_date', [$dateFrom, $dateTo])
    //         ->whereIn('status', ['Paid', 'Partially Paid'])
    //         ->where('invoice_type_id', 1)
    //         ->orderBy('invoice_date')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | TOTALS
    //     |--------------------------------------------------------------------------
    //     */
    //     $cashTotal = $cashInvoices->sum('paid_amount');

    //     $chargeTotal = $chargeInvoices->sum('paid_amount');

    //     $netSale = $cashTotal + $chargeTotal;

    //    return view('pdf.invoice_sales_report', [
    //         'cashInvoices' => $cashInvoices,
    //         'chargeInvoices' => $chargeInvoices,
    //         'cashTotal' => $cashTotal,
    //         'chargeTotal' => $chargeTotal,
    //         'netSale' => $netSale,
    //         'dateFrom' => $dateFrom,
    //         'dateTo' => $dateTo,
    //     ]);

        $dateFrom = Carbon::parse($request->date_from)
        ->startOfDay();

        $dateTo = Carbon::parse($request->date_to)
            ->endOfDay();

        $salesRepresentatives = $request->input(
            'sales_representative',
            []
        );

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
                'invoiceType',
            ])
            ->whereBetween('invoice_date', [
                $dateFrom,
                $dateTo,
            ])
            ->where('status', 'Paid')
            ->where('invoice_type_id', 2);

        /*
        |--------------------------------------------------------------------------
        | FILTER SALES REPRESENTATIVE
        |--------------------------------------------------------------------------
        */

        if (!empty($salesRepresentatives)) {
            $cashQuery->whereIn(
                'employee_id',
                $salesRepresentatives
            );
        }

        $cashInvoices = $cashQuery
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
                'invoiceType',
            ])
            ->whereBetween('invoice_date', [
                $dateFrom,
                $dateTo,
            ])
           ->whereIn('status', ['Paid', 'Partially Paid'])
            ->where('invoice_type_id', 1);

        /*
        |--------------------------------------------------------------------------
        | FILTER SALES REPRESENTATIVE
        |--------------------------------------------------------------------------
        */

        if (!empty($salesRepresentatives)) {
            $chargeQuery->whereIn(
                'employee_id',
                $salesRepresentatives
            );
        }

        $chargeInvoices = $chargeQuery
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


        return view('pdf.invoice_sales_report', [
            'cashInvoices' => $cashInvoices,
            'chargeInvoices' => $chargeInvoices,
            'cashTotal' => $cashTotal,
            'chargeTotal' => $chargeTotal,
            'netSale' => $netSale,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'salesRepresentatives' => $salesRepresentatives,
        ]);
    }

    
}
