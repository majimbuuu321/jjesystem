<?php

namespace App\Http\Controllers;
use App\Models\Warehouse;
use App\Models\InventoryPerWarehouse;
use App\Models\ProductCategory;
use App\Models\Products;
use Illuminate\Http\Request;

class InventoryPerWarehouseReportController extends Controller
{
    //

    // public function print(Request $request)
    // {
    //     $warehouseId = $request->warehouse_id;

    //     $dateFrom = $request->date_from;
    //     $dateTo = $request->date_to;

    //     $warehouse = Warehouse::find($warehouseId);

    //     $records = InventoryPerWarehouse::query()
    //         ->with([
    //             'product.category'
    //         ])
    //         ->where('warehouse_id', $warehouseId)
    //         ->get();

    //     $groupedRecords = $records->groupBy(function ($item) {
    //         return $item->product?->category?->product_category_name ?? 'Uncategorized';
    //     });

    //     return view('pdf.warehouse-inventory-summary', [
    //     'warehouse' => $warehouse,
    //     'dateFrom' => $dateFrom,
    //     'dateTo' => $dateTo,
    //     'records' =>$records,
    //     'groupedRecords' => $groupedRecords,
    // ]);
    // }

    public function print(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $productCategoryId = $request->product_category_id;
        $productIds = $request->product_id;

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // Convert "all" to null
        $warehouseId = $warehouseId === 'all' ? null : $warehouseId;
        $productCategoryId = $productCategoryId === 'all' ? null : $productCategoryId;
        if (is_string($productIds)) {
            $productIds = json_decode($productIds, true);
        }

        if (!is_array($productIds)) {
            $productIds = [];
        }

        if (in_array('all', $productIds)) {
            $productIds = [];
        }

        // Selected warehouse
        $warehouse = $warehouseId
            ? Warehouse::find($warehouseId)
            : null;

        $query = InventoryPerWarehouse::query()
            ->with([
                'warehouse',
                'product',
                'product.category',
            ]);

        // Warehouse filter
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        // Category filter
        if ($productCategoryId) {
            $query->whereHas('product', function ($query) use ($productCategoryId) {
                $query->where('product_category_id', $productCategoryId);
            });
        }

        // Product filter
        if (!empty($productIds)) {
            $query->whereIn('product_id', $productIds);
        }

        $records = $query
            ->orderBy('warehouse_id')
            ->orderBy('product_id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GROUP BY WAREHOUSE
        |--------------------------------------------------------------------------
        | Warehouse
        |   └── Category
        |        └── Products
        */
        $groupedRecords = $records
            ->groupBy(function ($item) {
                return $item->warehouse?->warehouse_name ?? 'Unknown Warehouse';
            })
            ->map(function ($warehouseItems) {

                return $warehouseItems->groupBy(function ($item) {
                    return $item->product?->category?->product_category_name
                        ?? 'Uncategorized';
                });

            });

        // Selected category
        $productCategory = $productCategoryId
            ? ProductCategory::find($productCategoryId)
            : null;

        // Selected product
        $product = $productIds
            ? Products::find($productIds)
            : null;

        return view('pdf.warehouse-inventory-summary', [
            'warehouse' => $warehouse,
            'productCategory' => $productCategory,
            'product_id' =>
            !isset($data['product_id']) ||
            empty($data['product_id']) ||
            in_array('all', $data['product_id'])
                ? null
                : json_encode($data['product_id']),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'records' => $records,
            'groupedRecords' => $groupedRecords,
        ]);
    }
}
