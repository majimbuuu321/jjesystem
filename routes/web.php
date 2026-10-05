<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryReportController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', 'admin/login');

Route::get(
    '/reports/inventory',
    [InventoryReportController::class, 'generate']
)->name('reports.inventory');