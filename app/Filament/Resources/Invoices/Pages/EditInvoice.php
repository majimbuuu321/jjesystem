<?php

namespace App\Filament\Resources\Invoices\Pages;
use App\Models\InventoryPerWarehouse;
use App\Models\StockMovements;
use App\Models\UnitOfMeasurement;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('post')
            ->label('Post Invoice')
            ->color('warning')
            ->icon('heroicon-m-check-circle')
            ->requiresConfirmation() 
            ->modalHeading('Post Invoice')
            ->modalDescription('Are you sure you want to post this? This action cannot be undone.')
            ->action(function () {

                foreach($this->record->InvoiceDetails as $item) {
                $productId = $item->products_id;
                $warehouseId = $this->record->warehouse_id;
                $quantity = $item->quantity;

                $uomCode = UnitOfMeasurement::where('id', $item->uom_id);
                // $unitCode = UnitOfMeasurement::find($item->uom_id)?->unit_code;
                // Check if the inventory record exists for the product and warehouse
                $inventory = InventoryPerWarehouse::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->first();
                if ($inventory) {
                    // Update the existing inventory record
                    $inventory->quantity -= $quantity;
                    $inventory->updated_at = now();
                    $inventory->save();
                } 
                // Create a new stock movement record
                StockMovements::create([
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'unit_code' => $uomCode->first()->unit_code,
                    'quantity' => $quantity,
                    'movement_type' => 'OUT',
                    'reference_note' => $this->record->id . ' - Sales Invoice No:' . $this->record->order_no,
                    'created_by' =>  auth()->user()->id,
                    'created_at' => now(),
                ]);
            }
                $this->record->update(['status' => 'Posted']);
                $this->getSavedNotification()?->send();
                $this->redirect(static::getUrl(['record' => $this->record]));
            })
            ->hidden(fn ($record) => in_array($record->status, [
                'Posted',
                'Partially Paid',
                'Paid',
            ])),

            Action::make('unpost')
            ->label('Unpost Invoice')
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
            ->modalHeading('Unpost Invoice')
            ->modalDescription(
                'Are you sure you want to unpost this invoice? ' .
                'The inventory deducted by this invoice will be restored ' .
                'and the invoice will be changed back to Draft.'
            )
            ->modalSubmitActionLabel('Yes, Unpost Invoice')
            ->visible(fn ($record) => $record->status === 'Posted' && Auth::user()?->hasRole('Admin'))
            ->action(function (array $data) {

                 // Verify current user's password
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
                DB::transaction(function () {

                    $invoice = $this->record;

                    foreach ($invoice->InvoiceDetails as $item) {

                        $productId = $item->products_id;
                        $warehouseId = $invoice->warehouse_id;
                        $quantity = (float) $item->quantity;

                        /*
                        * Get UOM code
                        */
                        $uomCode = UnitOfMeasurement::find(
                            $item->uom_id
                        )?->unit_code;

                        /*
                        * Find inventory
                        */
                        $inventory = InventoryPerWarehouse::where(
                                'product_id',
                                $productId
                            )
                            ->where(
                                'warehouse_id',
                                $warehouseId
                            )
                            ->lockForUpdate()
                            ->first();

                        if (!$inventory) {
                            throw new \Exception(
                                "Inventory not found for Product ID {$productId}."
                            );
                        }

                        /*
                        * RESTORE INVENTORY
                        *
                        * Post:
                        * quantity -= invoice quantity
                        *
                        * Unpost:
                        * quantity += invoice quantity
                        */
                        $inventory->quantity += $quantity;
                        $inventory->updated_at = now();
                        $inventory->save();

                        /*
                        * Create reverse stock movement
                        */
                        StockMovements::create([
                            'product_id' => $productId,
                            'warehouse_id' => $warehouseId,
                            'unit_code' => $uomCode,
                            'quantity' => $quantity,
                            'movement_type' => 'IN',
                            'reference_note' =>
                                $invoice->id .
                                ' - UNPOST Sales Invoice No: ' .
                                $invoice->order_no,
                            'created_by' => auth()->id(),
                            'created_at' => now(),
                        ]);
                    }

                    /*
                    * Change invoice back to Draft
                    */
                    $invoice->update([
                        'status' => 'Draft',
                        'updated_by' => auth()->id(),
                    ]);
                });

                /*
                * Success notification
                */
                Notification::make()
                    ->title('Invoice Unposted')
                    ->success()
                    ->body(
                        'Invoice ' .
                        $this->record->order_no .
                        ' has been changed back to Draft and inventory has been restored.'
                    )
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
}
