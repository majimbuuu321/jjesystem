<?php

namespace App\Http\Controllers;
use App\Models\InventoryHeader;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    //
    public function generate(Request $request)
    {
        // $request->validate([
        //     'date_from' => ['required', 'date'],
        //     'date_to' => ['required', 'date', 'after_or_equal:date_from'],
        //      'status' => ['required', 'in:All,Posted,Draft'],
        // ]);

        // $dateFrom = $request->date_from;
        // $dateTo = $request->date_to;
        // $status = $request->status;

        // $query = InventoryHeader::query()
        //     ->whereBetween('created_at', [
        //         $dateFrom . ' 00:00:00',
        //         $dateTo . ' 23:59:59',
        //     ])
        //     ->with([
        //         'warehouseFrom',
        //         'warehouseTo',
        //         'supplierFrom',
        //         'supplierTo',
        //         'InventoryDetail.product',
        //         'InventoryDetail.unitOfMeasurement',
        //     ]);

        //     // Only filter status when Posted or Draft is selected
        //     if ($status !== 'all') {
        //         $query->where('status', $status);
        //     }
        //      $records = $query
        //         ->orderBy('transfer_date')
        //         ->get();

        // return view('pdf.inventory_report', [
        // 'records' => $records,
        // 'dateFrom' => $dateFrom,
        // 'dateTo' => $dateTo,
        // 'status' => $status,
        // ]);

        $request->validate([
        'date_from' => ['required', 'date'],
        'date_to' => ['required', 'date', 'after_or_equal:date_from'],
        'status' => ['required', 'in:All,Posted,Draft'],
    ]);

    $dateFrom = $request->date_from;
    $dateTo = $request->date_to;
    $status = $request->status;

    $query = InventoryHeader::query()
        ->whereBetween('transfer_date', [
            $dateFrom . ' 00:00:00',
            $dateTo . ' 23:59:59',
        ])
        ->with([
            'warehouseFrom',
            'warehouseTo',
            'supplierFrom',
            'supplierTo',
            'InventoryDetail.product',
            'InventoryDetail.unitOfMeasurement',
        ]);
    if ($status === 'Posted') {
        $query->where('status', 'Posted');
    }

    if ($status === 'Draft') {
        $query->where('status', 'Draft');
    }

    $records = $query
        ->orderBy('transfer_date')
        ->get();

    return view('pdf.inventory_report', [
        'records' => $records,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'status' => $status,
    ]);
    }
}
