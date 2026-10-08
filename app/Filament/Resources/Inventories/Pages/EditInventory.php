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
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class EditInventory extends EditRecord
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('post')
            ->label('Post Inventory')
            ->color('warning')
            ->icon('heroicon-m-check-circle')
            ->requiresConfirmation() 
            ->modalHeading('Post Inventory')
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
                            'module' => 'Inventory',
                            'status' => 'Posted',
                            'created_by' =>  auth()->user()->id,
                            'created_at' => now(),
                        ]);
                    }
                    else if ($inventoryType->inventory_from == "SUPPLIER") {
                        StockMovements::create([
                            'product_id' => $productId,
                            'supplier_id' => $warehouseFromId,
                            'unit_code' => $uomCode->first()->unit_code,
                            'quantity' => $quantity,
                            'movement_type' => 'OUT',
                            'reference_note' => $this->record->id . ' - Document No:' . $this->record->document_no . ' - ' . $inventoryType->inventory_type,
                            'module' => 'Inventory',
                            'status' => 'Posted',
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
                            'module' => 'Inventory',
                            'status' => 'Posted',
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
                        'module' => 'Inventory',
                        'status' => 'Posted',
                        'created_by' =>  auth()->user()->id,
                        'created_at' => now(),
                    ]);
                }
            }
                
                $this->getSavedNotification()?->send();
                $this->redirect(static::getUrl(['record' => $this->record]));
            })
            ->hidden(fn ($record) => $record->status === 'Posted'),
            Action::make('unpost')
                ->label('Unpost Inventory')
                ->color('danger')
                ->icon('heroicon-m-arrow-uturn-left')

                ->form([
                    TextInput::make('password')
                        ->label('Enter Password')
                        ->password()
                        ->revealable()
                        ->required(),
                ])

                ->requiresConfirmation()
                ->modalHeading('Unpost Inventory')
                ->modalDescription(
                    'Are you sure you want to unpost this inventory? ' .
                    'The inventory deducted by this inventory will be restored ' .
                    'and the inventory will be changed back to Draft.'
                )
                ->visible(fn ($record) => $record->status === 'Posted' && Auth::user()?->hasRole('Admin'))

                ->action(function (array $data) {

                    // Validate password
                    if (! Hash::check(
                        $data['password'],
                        auth()->user()->password
                    )) {

                        Notification::make()
                            ->title('Invalid Password')
                            ->danger()
                            ->body('The password you entered is incorrect.')
                            ->send();

                        return;
                    }

                    $inventoryType = InventoryType::find($this->record->inventory_type_id);

                    foreach ($this->record->InventoryDetail as $item) {

                        $productId = $item->products_id;
                        $quantity = $item->quantity;
                        $uomId = $item->uom_id;

                        $warehouseFromId = $this->record->transfer_from;
                        $warehouseToId = $this->record->transfer_to;

                        $uomCode = UnitOfMeasurement::find($uomId)?->unit_code;

                        /*
                        * Reverse destination warehouse
                        */
                        if ($inventoryType->inventory_to == 'WAREHOUSE') {

                            $inventoryTo = InventoryPerWarehouse::where('product_id', $productId)
                                ->where('warehouse_id', $warehouseToId)
                                ->first();

                            if ($inventoryTo) {
                                $inventoryTo->quantity -= $quantity;
                                $inventoryTo->save();
                            }

                            StockMovements::create([
                                'product_id' => $productId,
                                'warehouse_id' => $warehouseToId,
                                'unit_code' => $uomCode,
                                'quantity' => $quantity,
                                'movement_type' => 'UNPOST-OUT',
                                'reference_note' => $this->record->id .
                                    ' - Document No:' . $this->record->document_no .
                                    ' - UNPOST ' . $inventoryType->inventory_type,
                                'module' => 'Inventory',
                                'status' => 'Unposted',
                                'created_by' => auth()->id(),
                                'created_at' => now(),
                            ]);
                        }

                        /*
                        * Restore source warehouse
                        */
                        if ($inventoryType->inventory_from == 'WAREHOUSE') {

                            $inventoryFrom = InventoryPerWarehouse::where('product_id', $productId)
                                ->where('warehouse_id', $warehouseFromId)
                                ->first();

                            if ($inventoryFrom) {

                                $inventoryFrom->quantity += $quantity;
                                $inventoryFrom->save();

                            } else {

                                InventoryPerWarehouse::create([
                                    'product_id' => $productId,
                                    'warehouse_id' => $warehouseFromId,
                                    'quantity' => $quantity,
                                    'unit_code' => $uomCode,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }

                            StockMovements::create([
                                'product_id' => $productId,
                                'warehouse_id' => $warehouseFromId,
                                'unit_code' => $uomCode,
                                'quantity' => $quantity,
                                'movement_type' => 'UNPOST-IN',
                                'reference_note' => $this->record->id .
                                    ' - Document No:' . $this->record->document_no .
                                    ' - UNPOST ' . $inventoryType->inventory_type,
                                'module' => 'Inventory',
                                'status' => 'Unposted',
                                'created_by' => auth()->id(),
                                'created_at' => now(),
                            ]);
                        }
                    }

                    $this->record->update([
                        'status' => 'Draft',
                    ]);

                    Notification::make()
                        ->title('Inventory Unposted')
                        ->success()
                        ->body('Inventory transaction has been successfully reversed.')
                        ->send();

                    $this->redirect(
                        static::getUrl([
                            'record' => $this->record,
                        ])
                    );
                })

                ->hidden(fn ($record) => $record->status !== 'Posted')
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }


}
