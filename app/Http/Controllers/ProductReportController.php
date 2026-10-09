<?php

namespace App\Http\Controllers;

use App\Models\InvoiceHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProductReportController extends Controller
{
    //
    public function salesReport(Request $request)
    {
        $dateFrom = Carbon::parse($request->date_from)->startOfDay();
        $dateTo = Carbon::parse($request->date_to)->endOfDay();

        $products = $request->input('product', []);

        /*
        |--------------------------------------------------------------------------
        | CASH INVOICES
        |--------------------------------------------------------------------------
        */

        $cashInvoices = InvoiceHeader::query()
            ->with([
                'customer',
                'employee',
                'paymentTerms',
                'route',
                'invoiceType',
                'InvoiceDetails.product',
            ])
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->where('status', 'Paid')
            ->where('invoice_type_id', 2)
            ->when(! empty($products), function ($query) use ($products) {
                $query->whereHas('InvoiceDetails', function ($query) use ($products) {
                    $query->whereIn('products_id', $products);
                });
            })
            ->orderBy('invoice_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CHARGE INVOICES
        |--------------------------------------------------------------------------
        */

        $chargeInvoices = InvoiceHeader::query()
            ->with([
                'customer',
                'employee',
                'paymentTerms',
                'route',
                'invoiceType',
                'InvoiceDetails.product',
            ])
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->where('invoice_type_id', 1)
            ->when(! empty($products), function ($query) use ($products) {
                $query->whereHas('InvoiceDetails', function ($query) use ($products) {
                    $query->whereIn('products_id', $products);
                });
            })
            ->orderBy('invoice_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GET CASH DETAILS
        |--------------------------------------------------------------------------
        */

        $cashDetails = $cashInvoices->flatMap(function ($invoice) use ($products) {

            return $invoice->InvoiceDetails
                ->when(! empty($products), function ($details) use ($products) {
                    return $details->filter(
                        fn ($detail) =>
                            in_array($detail->products_id, $products)
                    );
                })
                ->map(function ($detail) use ($invoice) {

                    $detail->report_invoice = $invoice;

                    return $detail;
                });
        });

        /*
        |--------------------------------------------------------------------------
        | GET CHARGE DETAILS
        |--------------------------------------------------------------------------
        */

        $chargeDetails = $chargeInvoices->flatMap(function ($invoice) use ($products) {

            return $invoice->InvoiceDetails
                ->when(! empty($products), function ($details) use ($products) {
                    return $details->filter(
                        fn ($detail) =>
                            in_array($detail->products_id, $products)
                    );
                })
                ->map(function ($detail) use ($invoice) {

                    $detail->report_invoice = $invoice;

                    return $detail;
                });
        });

        /*
        |--------------------------------------------------------------------------
        | GROUP BY PRODUCT
        |--------------------------------------------------------------------------
        */

        $cashByProduct = $cashDetails->groupBy(function ($detail) {
            return $detail->id;
        });

        $chargeByProduct = $chargeDetails->groupBy(function ($detail) {
            return $detail->id;
        });

        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        $cashTotal = $cashDetails->sum(function ($detail) {

            return (float) (
                $detail->net_amount
                ?? (
                    ($detail->quantity ?? 0)
                    * ($detail->price ?? 0)
                )
            );
        });

        $chargeTotal = $chargeDetails->sum(function ($detail) {

            return (float) (
                $detail->net_amount
                ?? (
                    ($detail->quantity ?? 0)
                    * ($detail->price ?? 0)
                )
            );
        });

        $netSale = $cashTotal + $chargeTotal;

        return view('pdf.sales_per_product', [
            'cashByProduct' => $cashByProduct,
            'chargeByProduct' => $chargeByProduct,
            'cashTotal' => $cashTotal,
            'chargeTotal' => $chargeTotal,
            'netSale' => $netSale,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }
}
