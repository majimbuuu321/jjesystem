<?php

namespace App\Http\Controllers;
use App\Models\InvoiceHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
class RouteReportController extends Controller
{
    //

     public function salesReport(Request $request)
    {
        $dateFrom = Carbon::parse($request->date_from)
            ->startOfDay();

        $dateTo = Carbon::parse($request->date_to)
            ->endOfDay();

        $routes = $request->input('route', []);

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

        if (!empty($routes)) {
            $cashQuery->whereIn(
                'route_id',
                $routes
            );
        }

        $cashInvoices = $cashQuery
            ->orderBy('route_id')
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

        if (!empty($routes)) {
            $chargeQuery->whereIn(
                'route_id',
                $routes
            );
        }

        $chargeInvoices = $chargeQuery
            ->orderBy('route_id')
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
        | GROUP BY ROUTE
        |--------------------------------------------------------------------------
        */

        $cashByRoute = $cashInvoices->groupBy('route_id');

        $chargeByRoute = $chargeInvoices->groupBy('route_id');

        return view('pdf.sales_per_route', [
                'cashInvoices' => $cashInvoices,
                'chargeInvoices' => $chargeInvoices,
                'cashTotal' => $cashTotal,
                'chargeTotal' => $chargeTotal,
                'netSale' => $netSale,
                'cashByRoute' => $cashByRoute,
                'chargeByRoute' => $chargeByRoute,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);
    }
}
