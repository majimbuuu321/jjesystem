<?php

namespace App\Filament\Resources\CreditMemos\Pages;
use App\Models\InventoryPerWarehouse;
use App\Models\StockMovements;
use App\Models\UnitOfMeasurement;
use App\Filament\Resources\CreditMemos\CreditMemoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class EditCreditMemo extends EditRecord
{
    protected static string $resource = CreditMemoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('post')
                ->label('Post Credit Memo')
                ->color('warning')
                ->icon('heroicon-m-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Post Credit Memo')
                ->modalDescription(
                    'Are you sure you want to post this? ' .
                    'The returned quantity will be added to the assigned warehouse.'
                )
                ->modalSubmitActionLabel('Yes, Post Credit Memo')
                ->visible(fn ($record) => $record->status !== 'Posted')
                ->action(function () {

                    $warehouseId = $this->record->warehouse_id;

                    foreach ($this->record->details as $item) {

                        $productId = $item->product_id;
                        $quantity = (float) $item->quantity;

                        /*
                        * Get Unit of Measurement
                        */
                        $unitCode = UnitOfMeasurement::find(
                            $item->uom_id
                        )?->unit_code;

                        /*
                        * Find inventory for Product + Warehouse
                        */
                        $inventory = InventoryPerWarehouse::where(
                            'product_id',
                            $productId
                        )
                        ->where(
                            'warehouse_id',
                            $warehouseId
                        )
                        ->first();

                        /*
                        * Increase inventory
                        */
                        if ($inventory) {

                            $inventory->quantity += $quantity;
                            $inventory->save();

                        } else {

                            /*
                            * Create inventory record if it does not exist
                            */
                            InventoryPerWarehouse::create([
                                'product_id' => $productId,
                                'warehouse_id' => $warehouseId,
                                'quantity' => $quantity,
                                'unit_code' => $unitCode,
                                'updated_at' => now(),
                            ]);
                        }

                        /*
                        * Create Stock Movement
                        */
                        StockMovements::create([
                            'product_id' => $productId,
                            'warehouse_id' => $warehouseId,
                            'unit_code' => $inventory->unit_code,
                            'quantity' => $quantity,
                            'movement_type' => 'IN',
                            'reference_note' =>
                                $this->record->id
                                . ' - CREDIT MEMO - CM No: '
                                . $this->record->credit_memo_no,
                            'module' => 'Credit Memo',
                            'status' => 'Posted',
                            'created_by' => auth()->user()->id,
                            'created_at' => now(),
                        ]);
                    }

                    /*
                    * Change status to Posted
                    */
                    $this->record->update([
                        'status' => 'Posted',
                        'posted_by' => auth()->user()->id,
                        'posted_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Credit Memo Posted')
                        ->body(
                            'Credit Memo has been posted and inventory has been increased.'
                        )
                        ->success()
                        ->send();

                    /*
                    * Stay on the same Credit Memo page
                    */
                    $this->redirect(
                        static::getUrl([
                            'record' => $this->record,
                        ])
                    );
                })
                ->hidden(fn ($record) => $record->status === 'Posted'),
                // ==========================================
                // UNPOST CREDIT MEMO
                // ==========================================

                Action::make('unpost')
                    ->label('Unpost Credit Memo')
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
                    ->modalHeading('Unpost Credit Memo')
                    ->modalDescription(
                        'Are you sure you want to unpost this Credit Memo? ' .
                        'The quantity added by this Credit Memo will be deducted ' .
                        'from the assigned warehouse.'
                    )
                    ->modalSubmitActionLabel('Yes, Unpost Credit Memo')

                    /*
                    * Only Admin can Unpost
                    */
                    ->visible(
                        fn ($record) =>
                            $record->status === 'Posted'
                            && Auth::user()?->hasRole('Admin')
                    )

                    ->action(function (array $data) {

                        // ==========================================
                        // VERIFY ADMIN PASSWORD
                        // ==========================================

                        if (! Hash::check(
                            $data['password'],
                            auth()->user()->password
                        )) {

                            Notification::make()
                                ->title('Invalid Password')
                                ->danger()
                                ->body(
                                    'The password you entered is incorrect.'
                                )
                                ->send();

                            return;
                        }


                        // ==========================================
                        // REVERSE INVENTORY
                        // ==========================================

                        $warehouseId = $this->record->warehouse_id;

                        foreach ($this->record->details as $item) {

                            $productId = $item->product_id;
                            $quantity = (float) $item->quantity;

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
                            ->first();

                            if ($inventory) {

                                /*
                                * Deduct quantity that was added
                                * when the Credit Memo was posted.
                                */
                                $inventory->quantity -= $quantity;

                                /*
                                * Prevent negative inventory
                                */
                                if ($inventory->quantity < 0) {
                                    $inventory->quantity = 0;
                                }

                                $inventory->save();
                            }

                            /*
                            * Get Unit of Measurement
                            */
                            $unitCode = UnitOfMeasurement::find(
                                $item->uom_id
                            )?->unit_code;

                            /*
                            * Create reversal Stock Movement
                            */
                            StockMovements::create([
                                'product_id' => $productId,
                                'warehouse_id' => $warehouseId,
                                'unit_code' => $inventory->unit_code,
                                'quantity' => $quantity,
                                'movement_type' => 'OUT',
                                'reference_note' =>
                                    $this->record->id
                                    . ' - CREDIT MEMO - CM No: '
                                    . $this->record->credit_memo_no
                                    . ' - UNPOST',
                                'module' => 'Credit Memo',
                                'status' => 'Unposted',
                                'created_by' => auth()->user()->id,
                                'created_at' => now(),
                            ]);
                        }


                        // ==========================================
                        // CHANGE STATUS BACK TO DRAFT
                        // ==========================================

                        $this->record->update([
                            'status' => 'Draft',
                            'posted_by' => null,
                            'posted_at' => null,
                        ]);


                        // ==========================================
                        // SUCCESS NOTIFICATION
                        // ==========================================

                        Notification::make()
                            ->title('Credit Memo Unposted')
                            ->body(
                                'The Credit Memo has been returned to Draft ' .
                                'and inventory has been deducted.'
                            )
                            ->success()
                            ->send();


                        /*
                        * Stay on the same Credit Memo page
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
