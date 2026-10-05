<?php

namespace App\Filament\Resources\Inventories\Pages;
use App\Models\InventoryPerWarehouse;
use App\Models\StockMovements;
use App\Models\UnitOfMeasurement;
use App\Models\InventoryType;
use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
class EditInventory extends EditRecord
{
    protected static string $resource = InventoryResource::class;

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

                foreach($this->record->InventoryDetail as $item) {
                $productId = $item->products_id;
                $warehouseId = $this->record->transfer_to;
                $quantity = $item->quantity;
                $uomId = $item->uom_id;
                $warehouseFromId = $this->record->transfer_from;

                $uomCode = UnitOfMeasurement::where('id', $uomId);
                // Check if the inventory product and warehouse
                $inventoryFrom = InventoryPerWarehouse::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseFromId)
                    ->first();
                $inventoryType = InventoryType::where('id', $this->record->inventory_type_id)->first();

                if ($inventoryFrom){
                    // Update the existing inventory record
                    $inventoryFrom->update(array($inventoryFrom->quantity -= $quantity));
                     // Create a new stock movement record
                    if($inventoryType->inventory_from == "WAREHOUSE")
                    {
                        StockMovements::create([
                            'product_id' => $productId,
                            'warehouse_id' => $warehouseFromId,
                            'unit_code' => $uomCode->first()->unit_code,
                            'quantity' => $quantity,
                            'movement_type' => 'OUT',
                            'reference_note' => $this->record->id . ' - Document No:' . $this->record->document_no . ' - ' . $inventoryType->inventory_type,
                            'created_by' =>  auth()->user()->id,
                            'created_at' => now(),
                        ]);
                    }
                    else if ($inventoryType->inventory_from = "SUPPLIER") {
                        StockMovements::create([
                            'product_id' => $productId,
                            'supplier_id' => $warehouseFromId,
                            'unit_code' => $uomCode->first()->unit_code,
                            'quantity' => $quantity,
                            'movement_type' => 'OUT',
                            'reference_note' => $this->record->id . ' - Document No:' . $this->record->document_no . ' - ' . $inventoryType->inventory_type,
                            'created_by' =>  auth()->user()->id,
                            'created_at' => now(),
                        ]);
                    }
                }

                // Check if the inventory record exists for the product and warehouse
                if($inventoryType->inventory_to == "WAREHOUSE")
                {
                     $inventory = InventoryPerWarehouse::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->first();
                    if ($inventory) {
                        // Update the existing inventory record
                        $inventory->update(array($inventory->quantity += $quantity));
                            // Create a new stock movement record
                       
                    } else {
                        // Create a new inventory record
                        InventoryPerWarehouse::create([
                            'product_id' => $productId,
                            'warehouse_id' => $warehouseId,
                            'quantity' => $quantity,
                            'unit_code' => $uomCode->first()->unit_code,
                            'updated_at' => now(),
                        ]);
                    }
                     StockMovements::create([
                            'product_id' => $productId,
                            'warehouse_id' => $warehouseId,
                            'unit_code' => $uomCode->first()->unit_code,
                            'quantity' => $quantity,
                            'movement_type' => 'IN',
                            'reference_note' => $this->record->id . ' - Document No:' . $this->record->document_no . ' - ' . $inventoryType->inventory_type,
                            'created_by' =>  auth()->user ()->id,
                            'created_at' => now(),
                        ]);
                }
                else if($inventoryType->inventory_to == "SUPPLIER"){
                      // Create a new stock movement record
                    StockMovements::create([
                        'product_id' => $productId,
                        'supplier_id' => $warehouseId,
                        'unit_code' => $uomCode->first()->unit_code,
                        'quantity' => $quantity,
                        'movement_type' => 'RTS',
                        'reference_note' => $this->record->id . ' - Document No:' . $this->record->document_no . ' - ' . $inventoryType->inventory_type,
                        'created_by' =>  auth()->user()->id,
                        'created_at' => now(),
                    ]);
                }
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
