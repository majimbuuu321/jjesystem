<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;
use App\Models\InventoryPerWarehouse;
use App\Models\StockMovements;
use App\Models\UnitOfMeasurement;
use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('post')
            ->label('Post Purchase Order')
            ->color('warning')
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
                    'reference_note' => $this->record->id . ' - PURCHASE ORDER - Invoice No:' . $this->record->invoice_no,
                    'module' => 'Purchase Order',
                    'status' => 'Posted',
                    'created_by' =>  auth()->user()->id,
                    'created_at' => now(),
                ]);
            }
                
                $this->getSavedNotification()?->send();
                $this->redirect(static::getUrl(['record' => $this->record]));
            })
            ->hidden(fn ($record) => $record->status === 'Posted'),


            // ==========================================
        // UNPOST PURCHASE ORDER
        // ==========================================
        Action::make('unpost')
            ->label('Unpost Purchase Order')
            ->color('danger')
            ->icon('heroicon-m-arrow-uturn-left')
            ->form([
                TextInput::make('password')
                    ->label('Admin Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->autocomplete('current-password'),
            ])
            ->requiresConfirmation()
            ->modalHeading('Unpost Purchase Order')
            ->modalDescription(
                'Are you sure you want to unpost this purchase order? ' .
                'The inventory deducted by this purchase order will be restored ' .
                'and the purchase order will be changed back to Draft.'
            )
            ->modalSubmitActionLabel('Yes, Unpost Purchase Order')
            ->visible(fn ($record) => $record->status === 'Posted' && Auth::user()?->hasRole('Admin'))

            ->action(function (array $data) {

                // ==========================================
                // VERIFY PASSWORD
                // ==========================================
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


                // ==========================================
                // REVERSE INVENTORY
                // ==========================================
                foreach ($this->record->PurchaseOrderDetail as $item) {

                    $productId = $item->products_id;
                    $warehouseId = $this->record->warehouse_id;
                    $quantity = (float) $item->quantity;

                    $inventory = InventoryPerWarehouse::where('product_id', $productId)
                        ->where('warehouse_id', $warehouseId)
                        ->first();

                    if ($inventory) {

                        // Subtract the quantity that was added during posting
                        $inventory->quantity -= $quantity;

                        // Prevent negative inventory
                        if ($inventory->quantity < 0) {
                            $inventory->quantity = 0;
                        }

                        $inventory->save();
                    }


                    // ==========================================
                    // CREATE REVERSAL STOCK MOVEMENT
                    // ==========================================
                    $unitCode = UnitOfMeasurement::find($item->uom_id)?->unit_code;

                    StockMovements::create([
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId,
                        'unit_code' => $unitCode,
                        'quantity' => $quantity,
                        'movement_type' => 'OUT',
                        'reference_note' => $this->record->id
                            . 'PURCHASE ORDER - PO No:'
                            . $this->record->purchase_order_no
                            . ' - UNPOST',
                        'module' => 'Purchase Order',
                        'status' => 'Unposted',
                        'created_by' => auth()->user()->id,
                        'created_at' => now(),
                    ]);
                }


                // ==========================================
                // CHANGE STATUS BACK TO DRAFT
                // ==========================================
                $this->record->update([
                    'status' => 'Draft'
                ]);


                // ==========================================
                // SUCCESS NOTIFICATION
                // ==========================================
                Notification::make()
                    ->title('Purchase Order Unposted')
                    ->body('The Purchase Order has been returned to Draft and inventory has been reversed.')
                    ->success()
                    ->send();
                /*
                * Stay on the same Invoice View page
                */
                $this->redirect(
                    static::getUrl([
                        'record' => $this->record,
                    ])
                );
            }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
