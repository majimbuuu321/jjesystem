<?php

namespace App\Filament\Resources\CreditMemos\RelationManagers;
use App\Models\Products;
use App\Models\InvoiceDetail;
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
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';
    protected static ?string $title = 'Credit Memo Details';
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Product')
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'product_description',
                        modifyQueryUsing: function (Builder $query) {

                            $creditMemo = $this->getOwnerRecord();

                            if (!$creditMemo || !$creditMemo->invoice_id) {
                                $query->whereRaw('1 = 0');
                                return;
                            }

                            // Get products that exist in the selected invoice
                            $productIds = InvoiceDetail::query()
                                ->where('invoice_header_id', $creditMemo->invoice_id)
                                ->pluck('products_id')
                                ->unique()
                                ->values()
                                ->toArray();

                            if (empty($productIds)) {
                                $query->whereRaw('1 = 0');
                                return;
                            }

                            $query->whereIn('id', $productIds);
                        }
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) =>
                            $record->product_code . ' - ' . $record->product_description
                    )
                    ->searchable(['product_code', 'product_description'])
                    ->preload()
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {

                        if (!$state) {
                            $set('quantity', 0);
                            $set('selling_price', 0);
                             $set('weight', 0);
                            $set('unit_cost', 0);
                            $set('amount', 0);
                            $set('total_cost', 0);
                            $set('tag_weight', 0);
                            $set('quantity_invoice', 0);
                             $set('remarks', null);
                            return;
                        }

                        $creditMemo = $this->getOwnerRecord();

                        if (!$creditMemo || !$creditMemo->invoice_id) {
                            return;
                        }

                        // Get the selected product from the selected invoice
                        $detail = InvoiceDetail::query()
                            ->where('invoice_header_id', $creditMemo->invoice_id)
                            ->where('products_id', $state)
                            ->first();

                        $prodData = Products::query()
                            ->where('id', $state)
                            ->first();

                        if (!$detail) {
                            return;
                        }

                        // Default quantity
                        $set('quantity_invoice', $detail->quantity ?? 0);
                        // Get selling price from invoice detail
                        $set(
                            'selling_price',
                            $detail->price ?? 0
                        );

                        $set(
                            'tag_weight',
                            $detail->tag_weight ?? 0
                        );

                        $set(
                            'unit_cost',
                            $prodData->unit_cost ?? 0
                        );

                        
                    }),

                TextInput::make('quantity_invoice')
                        ->label('Quantity in Invoice')
                        ->readOnly()
                        ->dehydrated()
                        ->afterStateHydrated(function ($state, callable $set, callable $get) {

                            if ($state !== null) {
                                return;
                            }

                            $productId = $get('product_id');

                            if (!$productId) {
                                return;
                            }

                            $creditMemo = $this->getOwnerRecord();

                            if (!$creditMemo?->invoice_id) {
                                return;
                            }

                            $invoiceDetail = InvoiceDetail::query()
                                ->where('invoice_header_id', $creditMemo->invoice_id)
                                ->where('products_id', $productId)
                                ->first();

                            if ($invoiceDetail) {
                                $set('quantity_invoice', $invoiceDetail->quantity ?? 0);
                            }
                        }),   

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->rule(function (Get $get) {
                        return 'max:' . ((float) $get('quantity_invoice'));
                    })
                    ->validationMessages([
                        'max' => 'Quantity cannot be greater than the invoice quantity.',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {

                        $quantity = (float) ($state ?? 0);

                        $tagWeight = (float) ($get('tag_weight') ?? 0);
                        $unitCost = (float) ($get('unit_cost') ?? 0);
                        $sellingPrice = (float) ($get('selling_price') ?? 0);

                        $set('weight', round($quantity * $tagWeight, 3));
                        $set('total_cost', round($quantity * $unitCost, 2));
                        $set('amount', round($quantity * $sellingPrice, 2));
                    }),

                TextInput::make('tag_weight')
                    ->label('Tag Weight')
                    ->numeric()
                    ->readOnly()
                    ->dehydrated()
                    ->afterStateHydrated(function ($state, callable $set, callable $get) {

                        if ($state !== null) {
                            return;
                        }

                        $productId = $get('product_id');

                        if (!$productId) {
                            return;
                        }

                        $creditMemo = $this->getOwnerRecord();

                        if (!$creditMemo?->invoice_id) {
                            return;
                        }

                        $invoiceDetail = InvoiceDetail::query()
                            ->where('invoice_header_id', $creditMemo->invoice_id)
                            ->where('products_id', $productId)
                            ->first();

                        if ($invoiceDetail) {
                            $set('tag_weight', $invoiceDetail->tag_weight ?? 0);
                        }
                    }),

                TextInput::make('weight')
                    ->label('Total Weight (kg)')
                    ->readOnly()
                    ->required()
                    ->dehydrated(),

                TextInput::make('unit_cost')
                    ->label('Unit Cost')
                    ->required()
                    ->readOnly()
                    ->dehydrated()
                    ->prefix('₱'),
                
                TextInput::make('total_cost')
                    ->label('Total Cost')
                    ->required()
                    ->readOnly()
                    ->dehydrated()
                    ->prefix('₱'),

                TextInput::make('selling_price')
                    ->label('Selling Price')
                    ->required()
                    ->readOnly()
                    ->dehydrated()
                    ->prefix('₱'),

                TextInput::make('amount')
                    ->label('Amount')
                    ->readOnly()
                    ->dehydrated()
                    ->prefix('₱'),

                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.product_description')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Quantity'),

                TextColumn::make('weight')
                    ->label('Weight')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->money('PHP'),
                
                TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->money('PHP'),

                TextColumn::make('selling_price')
                    ->label('Selling Price')
                    ->money('PHP'),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PHP'),

                TextColumn::make('remarks')
                    ->label('Remarks'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->createAnother(false)
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft')
                ->label('Add Credit Memo Detail'),
            ])
            ->recordActions([
                EditAction::make()
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
                DeleteAction::make()
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
            ])
            ->toolbarActions([
            ]);
    }
}
