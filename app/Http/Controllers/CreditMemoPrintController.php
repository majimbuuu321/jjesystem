<?php

namespace App\Http\Controllers;
use App\Models\CreditMemoHeader;
use App\Models\InventoryPerWarehouse;
use Illuminate\Http\Request;

class CreditMemoPrintController extends Controller
{
    //
    public function print(CreditMemoHeader $creditMemo)
    {
       $creditMemo->load([
        'customer',
        'warehouse',
        'invoice',
        'details.product',
        ]);

        $warehouseId = $creditMemo->warehouse_id;

        foreach ($creditMemo->details as $detail) {
            $inventory = \App\Models\InventoryPerWarehouse::query()
                ->where('product_id', $detail->product_id)
                ->where('warehouse_id', $warehouseId)
                ->first();

            $detail->inventory_unit = $inventory?->unit_code ?? 'N/A';
        }

        return view('pdf.credit_memo', [
            'creditMemo' => $creditMemo,
        ]);
    }
}
