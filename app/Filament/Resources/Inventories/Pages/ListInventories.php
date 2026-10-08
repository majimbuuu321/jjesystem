<?php

namespace App\Filament\Resources\Inventories\Pages;
use App\Models\InventoryHeader;
use App\Models\Warehouse;
use App\Models\ProductCategory;
use App\Models\Products;
use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Add Inventory'),
            Action::make('inventoryReport')
            ->label('Inventory Report')
            ->icon('heroicon-o-document-chart-bar')
            ->color('danger')
            ->form([

                Select::make('report_type')
                    ->label('Report')
                    ->searchable()
                    ->live()
                    ->options([
                        'inventory_per_detail' => 'Inventory per Detail',
                        'inventory_per_warehouse' => 'Inventory per Warehouse',
                    ])
                    ->required(),

                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->options(function () {
                        return [
                            'all' => 'All Warehouses',
                        ] + Warehouse::where('status', 'Active')
                            ->orderBy('warehouse_name')
                            ->pluck('warehouse_name', 'id')
                            ->toArray();
                    })
                    ->default('all')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->visible(fn ($get) => $get('report_type') === 'inventory_per_warehouse')
                    ->required(fn ($get) => $get('report_type') === 'inventory_per_warehouse'),

                Select::make('product_category_id')
                    ->label('Product Category')
                    ->options(function () {
                        return [
                            'all' => 'All Categories',
                        ] + ProductCategory::where('status', 'Active')
                            ->orderBy('product_category_name')
                            ->pluck('product_category_name', 'id')
                            ->toArray();
                    })
                    ->default('all')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {

                        // Always reset products when category changes
                        $set('product_id', ['all']);

                    })
                    ->visible(fn (Get $get) => $get('report_type') === 'inventory_per_warehouse')
                    ->required(fn (Get $get) => $get('report_type') === 'inventory_per_warehouse'),


                Select::make('product_id')
                    ->label('Product')
                    ->options(function (Get $get) {

                        $categoryId = $get('product_category_id');

                        // All Categories
                        if (!$categoryId || $categoryId === 'all') {
                            return [
                                'all' => 'All Products',
                            ];
                        }

                        // Specific Category
                        return [
                            'all' => 'All Products',
                        ] + Products::query()
                            ->where('status', 'Active')
                            ->where('product_category_id', $categoryId)
                            ->orderBy('product_description')
                            ->pluck('product_description', 'id')
                            ->toArray();
                    })
                    ->default(['all'])
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->live()

                    // Disable product selection when All Categories
                    ->disabled(fn (Get $get) =>
                        !$get('product_category_id') ||
                        $get('product_category_id') === 'all'
                    )

                    ->afterStateUpdated(function ($state, Set $set) {

                        // If All Products is selected,
                        // remove all other selected products.
                        if (
                            is_array($state) &&
                            in_array('all', $state)
                        ) {
                            $set('product_id', ['all']);
                        }

                    })

                    ->visible(fn (Get $get) =>
                        $get('report_type') === 'inventory_per_warehouse'
                    )

                    ->required(fn (Get $get) =>
                        $get('report_type') === 'inventory_per_warehouse'
                    ),

                DatePicker::make('date_from')
                    ->label('Date From')
                    ->required()
                    ->default(now()->startOfMonth()),

                DatePicker::make('date_to')
                    ->label('Date To')
                    ->required()
                    ->default(now()),

                Select::make('status')
                    ->label('Status')
                    ->searchable()
                    ->options([
                        'All' => 'All',
                        'Posted' => 'Posted',
                        'Draft' => 'Draft',
                    ])
                    ->default('All')
                    ->required()
                    ->visible(fn ($get) => $get('report_type') === 'inventory_per_detail'),
            ])
            // ->action(function (array $data) {

            //     $url = match ($data['report_type']) {

            //         'inventory_per_detail' => route(
            //             'reports.inventory',
            //             [
            //                 'date_from' => $data['date_from'],
            //                 'date_to' => $data['date_to'],
            //                 'status' => $data['status'],
            //             ]
            //         ),

            //         'inventory_per_warehouse' => route(
            //             'reports.warehouse-inventory-summary',
            //             [
            //                 'warehouse_id' => $data['warehouse_id'],
            //                 'date_from' => $data['date_from'],
            //                 'date_to' => $data['date_to'],
            //             ]
            //         ),

            //         default => null,
            //     };

            //     if ($url) {
            //         $this->js(
            //             "window.open(" . json_encode($url) . ", '_blank')"
            //         );
            //     }
            // })
            ->action(function (array $data) {

                $url = match ($data['report_type']) {

                    'inventory_per_detail' => route(
                        'reports.inventory',
                        [
                            'date_from' => $data['date_from'] ?? null,
                            'date_to'   => $data['date_to'] ?? null,
                            'status'    => $data['status'] ?? null,
                        ]
                    ),

                    'inventory_per_warehouse' => route(
                        'reports.warehouse-inventory-summary',
                        [
                            'warehouse_id' =>
                                ($data['warehouse_id'] ?? 'all') === 'all'
                                    ? null
                                    : $data['warehouse_id'],

                            'product_category_id' =>
                                ($data['product_category_id'] ?? 'all') === 'all'
                                    ? null
                                    : $data['product_category_id'],

                            'product_id' =>
                                !isset($data['product_id']) ||
                                empty($data['product_id']) ||
                                in_array('all', $data['product_id'])
                                    ? null
                                    : $data['product_id'],

                            'date_from' => $data['date_from'] ?? null,
                            'date_to'   => $data['date_to'] ?? null,
                        ]
                    ),

                    default => null,
                };

                if ($url) {
                    $this->js(
                        "window.open(" . json_encode($url) . ", '_blank')"
                    );
                }
            })
            
        ];
    }
}
