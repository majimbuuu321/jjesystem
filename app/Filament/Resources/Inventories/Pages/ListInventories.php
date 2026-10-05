<?php

namespace App\Filament\Resources\Inventories\Pages;
use App\Models\InventoryHeader;
use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Torgodly\Html2Media\Actions\Html2MediaAction;
class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            // Action::make('inventoryReport')
            //     ->label('Inventory Report')
            //     ->icon('heroicon-o-document-chart-bar')
            //     ->form([
            //         DatePicker::make('date_from')
            //             ->label('Date From')
            //             ->required()
            //             ->default(now()->startOfMonth()),

            //         DatePicker::make('date_to')
            //             ->label('Date To')
            //             ->required()
            //             ->default(now()),
            //     ])
            //     ->action(function (array $data) {
            //         $dateFrom = $data['date_from'];
            //         $dateTo = $data['date_to'];

            //         $records = InventoryHeader::query()
            //             ->whereBetween('created_at', [
            //                 $dateFrom . ' 00:00:00',
            //                 $dateTo . ' 23:59:59',
            //             ])
            //             ->with([
            //                 'warehouseFrom',
            //                 'warehouseTo',
            //                 'supplierFrom',
            //                 'supplierTo',
            //                 'InventoryDetail.product',
            //                 'InventoryDetail.unitOfMeasurement',
            //             ])
            //             ->orderBy('created_at')
            //             ->get();

            //         return response()->streamDownload(function () use (
            //             $records,
            //             $dateFrom,
            //             $dateTo
            //         ) {
            //             echo view('pdf.inventory_report', [
            //                 'records' => $records,
            //                 'dateFrom' => $dateFrom,
            //                 'dateTo' => $dateTo,
            //             ])->render();
            //         }, 'inventory-report.html');
            //     })
        ];
    }
}
