<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;
use App\Models\InventoryPerWarehouse;
use App\Models\StockMovements;
use App\Models\UnitOfMeasurement;
use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('post')
            ->label('Post Purchase Order')
            ->color('danger')
            ->icon('heroicon-m-check-circle')
            ->requiresConfirmation() 
            ->modalHeading('Post Purchase Order')
            ->modalDescription('Are you sure you want to post this? This action cannot be undone.')
            ->action(function () {
                $this->record->update(['status' => 'Posted']);

                foreach($this->record->PurchaseOrderDetail as $item) {
                $productId = $item->products_id;
                $warehouseId = $this->record->warehouse_id;
                $quantity = $item->quantity;
                $unitCode = UnitOfMeasurement::find($item->uom_id)?->unit_code;
                // Check if the inventory record exists for the product and warehouse
                $inventory = InventoryPerWarehouse::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->first();
                if ($inventory) {
                    // Update the existing inventory record
                    $inventory->quantity += $quantity;
                    $inventory->save();
                } else {
                    // Create a new inventory record
                    InventoryPerWarehouse::create([
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId,
                        'quantity' => $quantity,
                        'unit_code' => $unitCode,
                        'updated_at' => now(),
                    ]);
                }

                // Create a new stock movement record
                StockMovements::create([
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'unit_code' => $unitCode,
                    'quantity' => $quantity,
                    'movement_type' => 'IN',
                    'reference_note' => $this->record->id . ' - Invoice No:' . $this->record->invoice_no,
                    'created_by' =>  auth()->user()->id,
                    'created_at' => now(),
                ]);
            }
                
                $this->getSavedNotification()?->send();
                return redirect($this->getResource()::getUrl('index'));
            })
            ->hidden(fn ($record) => $record->status === 'Posted'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
