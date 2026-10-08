<?php

namespace App\Http\Controllers;

use App\Models\InvoiceHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CategoryReportController extends Controller
{
    //
    public function salesReport(Request $request)
    {
        $dateFrom = Carbon::parse($request->date_from)
            ->startOfDay();

        $dateTo = Carbon::parse($request->date_to)
            ->endOfDay();

        $categories = $request->input('category', []);

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
                'InvoiceDetails.product.category',
            ])
            ->whereBetween('invoice_date', [
                $dateFrom,
                $dateTo,
            ])
            ->where('status', 'Paid')
            ->where('invoice_type_id', 2)
            ->when(!empty($categories), function ($query) use ($categories) {

                $query->whereHas('InvoiceDetails.product', function ($query) use ($categories) {

                    $query->whereIn(
                        'product_category_id',
                        $categories
                    );

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
                'InvoiceDetails.product.category',
            ])
            ->whereBetween('invoice_date', [
                $dateFrom,
                $dateTo,
            ])
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->where('invoice_type_id', 1)
            ->when(!empty($categories), function ($query) use ($categories) {

                $query->whereHas('InvoiceDetails.product', function ($query) use ($categories) {

                    $query->whereIn(
                        'product_category_id',
                        $categories
                    );

                });

            })
            ->orderBy('invoice_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | GET CATEGORY DETAILS
        |--------------------------------------------------------------------------
        */

        $cashDetails = $cashInvoices
            ->flatMap(function ($invoice) {

                return $invoice->InvoiceDetails->map(function ($detail) use ($invoice) {

                    $detail->report_invoice = $invoice;

                    return $detail;

                });

            });


        $chargeDetails = $chargeInvoices
            ->flatMap(function ($invoice) {

                return $invoice->InvoiceDetails->map(function ($detail) use ($invoice) {

                    $detail->report_invoice = $invoice;

                    return $detail;

                });

            });


        /*
        |--------------------------------------------------------------------------
        | FILTER SELECTED CATEGORIES
        |--------------------------------------------------------------------------
        */

        if (!empty($categories)) {

            $cashDetails = $cashDetails
                ->filter(function ($detail) use ($categories) {

                    return in_array(
                        $detail->product?->product_category_id,
                        $categories
                    );

                });

            $chargeDetails = $chargeDetails
                ->filter(function ($detail) use ($categories) {

                    return in_array(
                        $detail->product?->product_category_id,
                        $categories
                    );

                });

        }


        /*
        |--------------------------------------------------------------------------
        | GROUP BY CATEGORY
        |--------------------------------------------------------------------------
        */

        $cashByCategory = $cashDetails
            ->groupBy(function ($detail) {

                return $detail->product?->product_category_id;

            });

        $chargeByCategory = $chargeDetails
            ->groupBy(function ($detail) {

                return $detail->product?->product_category_id;

            });


        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        $cashTotal = $cashDetails->sum(function ($detail) {

            return (float) (
                $detail->amount
                ?? (
                    ($detail->quantity ?? 0)
                    * ($detail->price ?? 0)
                )
            );

        });


        $chargeTotal = $chargeDetails->sum(function ($detail) {

            return (float) (
                $detail->amount
                ?? (
                    ($detail->quantity ?? 0)
                    * ($detail->price ?? 0)
                )
            );

        });



        $netSale = $cashTotal + $chargeTotal;


        return view('pdf.sales_per_category', [
                'cashByCategory' => $cashByCategory,
                'chargeByCategory' => $chargeByCategory,
                'cashTotal' => $cashTotal,
                'chargeTotal' => $chargeTotal,
                'netSale' => $netSale,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);
    }
}
