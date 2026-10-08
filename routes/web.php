<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\InventoryPerWarehouseReportController;
use App\Http\Controllers\InvoicePrintController;
use App\Http\Controllers\CreditMemoPrintController;
use App\Http\Controllers\InvoiceReportController;
use App\Http\Controllers\RouteReportController;
use App\Http\Controllers\CustomerReportController;
use App\Http\Controllers\CategoryReportController;
use App\Http\Controllers\ProductReportController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', 'admin/login');

Route::get(
    '/reports/inventory',
    [InventoryReportController::class, 'generate']
)->name('reports.inventory');


Route::get(
    '/reports/warehouse-inventory-summary',
    [InventoryPerWarehouseReportController::class, 'print']
)->name('reports.warehouse-inventory-summary');

Route::get('/invoice/{invoice}/print', [
    InvoicePrintController::class,
    'print'
])->name('invoice.print');

Route::get('/credit-memo/{creditMemo}/print', [
    CreditMemoPrintController::class,
    'print',
])->name('credit-memo.print');

Route::get(
    '/reports/invoice-sales',
    [InvoiceReportController::class, 'salesReport']
)->name('invoice.sales-report');

Route::get(
    '/reports/sales-per-route',
    [RouteReportController::class, 'salesReport']
)->name('invoice.route-report');

Route::get(
    '/reports/sales-per-customer',
    [CustomerReportController::class, 'salesReport']
)->name('invoice.customer-report');

Route::get(
    '/reports/sales-per-category',
    [CategoryReportController::class, 'salesReport']
)->name('invoice.category-report');

Route::get(
    '/reports/sales-per-product',
    [ProductReportController::class, 'salesReport']
)->name('invoice.product-report');