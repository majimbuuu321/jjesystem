<?php

namespace App\Filament\Resources\Invoices\RelationManagers;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Closure;
class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                         ->schema([
                            DatePicker::make('payment_date')
                                ->label('Payment Date')
                                ->required()
                                ->default(now())
                                ->maxDate(now()),

                            // TextInput::make('balance_amount')
                            // ->label('Remaining Balance')
                            // ->prefix('₱')
                            // ->numeric()
                            // ->disabled()
                            // ->dehydrated(false)
                            // ->default(function ($livewire) {
                            //     return $livewire->ownerRecord->balance_amount;
                            // }),

                            // TextInput::make('balance_amount')
                            // ->label('Remaining Balance')
                            // ->prefix('₱')
                            // ->numeric()
                            // ->disabled()
                            // ->dehydrated(false)
                            // ->afterStateHydrated(function ($component, $livewire, $record) {

                            //     $invoice = $livewire->ownerRecord;

                            //     $currentBalance = (float) $invoice->balance_amount;
                            //     $oldPayment = (float) ($record?->payment_amount ?? 0);

                            //     $availableBalance = $currentBalance + $oldPayment;

                            //     $component->state($availableBalance);
                            // }),
                            TextInput::make('balance_amount')
                            ->label('Remaining Balance')
                            ->prefix('₱')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $livewire) {

                                $invoice = $livewire->ownerRecord;

                                // Get the actual total of ALL payment logs
                                $totalPaid = (float) $invoice->payments()
                                    ->sum('payment_amount');

                                // Calculate actual remaining balance
                                $remainingBalance =
                                    (float) $invoice->total_amount - $totalPaid;

                                // Prevent negative balance
                                $remainingBalance = max(0, $remainingBalance);

                                $component->state($remainingBalance);
                            }),
                         ]),
                         Grid::make(3)
                         ->schema([
                            TextInput::make('payment_amount')
                                ->numeric()
                                ->required()
                                ->minValue(0.01)
                                ->prefix('₱')
                                ->rules([
                                    function ($livewire) {
                                        return function (string $attribute, $value, Closure $fail) use ($livewire) {

                                            $invoice = $livewire->ownerRecord;

                                            $paymentAmount = (float) $value;
                                            $balanceAmount = (float) $invoice->balance_amount;

                                            if ($paymentAmount > $balanceAmount) {
                                                $fail(
                                                    'Payment amount cannot exceed the remaining balance of '
                                                    . number_format($balanceAmount, 2)
                                                );
                                            }
                                        };
                                    },
                                ])
                                ->validationMessages([
                                    'required' => 'Payment amount is required.',
                                    'min' => 'Payment amount must be greater than zero.',
                            ]),
                                
                            Select::make('payment_method')
                                ->required()
                                ->searchable()
                                ->options([
                                    'CASH' => 'Cash',
                                    'GCASH' => 'GCash',
                                    'MAYA' => 'Maya',
                                    'BANK' => 'Bank Transfer',
                                    'CHEQUE' => 'Cheque',
                                    'CC' => 'Credit Card',
                                    'DC' => 'Debit Card',
                                ])
                                ->label('Payment Method')
                                ->loadingMessage('Loading Payment Method...'),
                            
                            TextInput::make('reference_no')
                                ->label('Reference No.')
                                ->default(null),
                         ]),
                    ])->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment_date')
            ->columns([
                TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->sortable(),
                
                TextColumn::make('payment_amount')
                    ->label('Payment Amount')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->sortable(),
                
                TextColumn::make('reference_no')
                    ->label('Reference No.'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->label('Add Payments')
                ->createAnother(false)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
                })
                ->after(function ($record, $livewire) {

                    $invoice = $livewire->ownerRecord;

                    $paymentAmount = (float) $record->payment_amount;

                    $invoice->paid_amount =
                        (float) $invoice->paid_amount + $paymentAmount;

                    $invoice->balance_amount =
                        (float) $invoice->balance_amount - $paymentAmount;

                    if ($invoice->balance_amount < 0) {
                        $invoice->balance_amount = 0;
                    }

                    /*
                    * Update status
                    */
                    if ($invoice->balance_amount <= 0 && $invoice->paid_amount > 0) {
                        $invoice->status = 'Paid';
                    } elseif ($invoice->paid_amount > 0) {
                        $invoice->status = 'Partially Paid';
                    } else {
                        $invoice->status = 'Posted';
                    }
                    $invoice->save();
                    // Refresh the Invoice Edit page
                    $livewire->redirect(
                        InvoiceResource::getUrl('edit', [
                            'record' => $invoice,
                        ]) . '?relation=1'
                    );
                }),
            ])
            ->recordActions([
                EditAction::make()
                ->before(function ($record, array $data, $livewire) {

                    $invoice = $livewire->ownerRecord;

                    // Payment amount before editing
                    $oldPayment = (float) $record->payment_amount;

                    // Payment amount entered by user
                    $newPayment = (float) ($data['payment_amount'] ?? 0);

                    // Current balance already includes the old payment
                    $availableBalance =
                        (float) $invoice->balance_amount + $oldPayment;

                    // Don't allow payment greater than available balance
                    if ($newPayment > $availableBalance) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'payment_amount' =>
                                'Payment amount cannot exceed the remaining balance of '
                                . number_format($availableBalance, 2),
                        ]);
                    }

                    // Store old amount so after() can use it
                    $livewire->oldPaymentAmount = $oldPayment;
                })

                ->after(function ($record, $livewire) {

                    $invoice = $livewire->ownerRecord;

                    // Old payment before edit
                    $oldPayment = (float) $livewire->oldPaymentAmount;

                    // New payment after edit
                    $newPayment = (float) $record->payment_amount;

                    // Difference
                    $difference = $newPayment - $oldPayment;

                    // Update paid amount
                    $invoice->paid_amount =
                        (float) $invoice->paid_amount + $difference;

                    // Update balance
                    $invoice->balance_amount =
                        (float) $invoice->balance_amount - $difference;

                    // Prevent negative values
                    if ($invoice->paid_amount < 0) {
                        $invoice->paid_amount = 0;
                    }

                    if ($invoice->balance_amount < 0) {
                        $invoice->balance_amount = 0;
                    }

                    /*
                    * Update status
                    */
                    if ($invoice->balance_amount <= 0 && $invoice->paid_amount > 0) {
                        $invoice->status = 'Paid';
                    } elseif ($invoice->paid_amount > 0) {
                        $invoice->status = 'Partially Paid';
                    } else {
                        $invoice->status = 'Posted';
                    }

                    $invoice->save();
                    $livewire->redirect(
                        InvoiceResource::getUrl('edit', [
                            'record' => $invoice,
                        ]) . '?relation=1'
                    );
                })
                ->mutateFormDataUsing(function (array $data): array {
                    $data['updated_by'] = auth()->id();
                    return $data;
                }),
                DeleteAction::make()
                 ->before(function ($record, $livewire) {
                    $invoice = $livewire->ownerRecord;

                    $paymentAmount = (float) $record->payment_amount;

                    /*
                    * Return the payment amount back to the balance.
                    */
                    $invoice->paid_amount =
                        (float) $invoice->paid_amount - $paymentAmount;

                    $invoice->balance_amount =
                        (float) $invoice->balance_amount + $paymentAmount;

                    /*
                    * Prevent negative paid amount.
                    */
                    if ($invoice->paid_amount < 0) {
                        $invoice->paid_amount = 0;
                    }

                    if ($invoice->balance_amount <= 0 && $invoice->paid_amount > 0) {
                        $invoice->status = 'Paid';
                    } elseif ($invoice->paid_amount > 0) {
                        $invoice->status = 'Partially Paid';
                    } else {
                        $invoice->status = 'Posted';
                    }

                    $invoice->save();
                    $livewire->redirect(
                       InvoiceResource::getUrl('edit', [
                            'record' => $invoice,
                        ]) . '?relation=1'
                    );
                }),
            ])
            ->toolbarActions([
                
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->status !== 'Draft';
    }
}
