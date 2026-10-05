<?php

namespace App\Filament\Resources\Inventories\Schemas;
use App\Models\InventoryHeader;
use App\Models\Employee;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\InventoryType;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make()
                ->schema([
                    Grid::make(4)
                        ->schema([
                            DatePicker::make('transfer_date')
                            ->required()
                            ->label('Transfer Date')
                            ->minDate(now()->subYears(150))
                            ->maxDate(now())
                            ->default(now()),
                        ]),
                    Grid::make(3)
                        ->schema([
                            TextInput::make('document_no')
                                ->required()
                                ->label('Document No.')
                                ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                        return '';
                                        }
                                    return strtoupper($state);
                                }),

                            TextInput::make('plate_no')
                                ->label('Plate No.')
                                ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                        return '';
                                        }
                                    return strtoupper($state);
                                }),

                                Select::make('inventory_type_id')
                                ->options(InventoryType::all()->pluck('inventory_type', 'id'))
                                ->required()
                                ->searchable()
                                ->reactive()
                                ->label('Inventory Type')
                                ->loadingMessage('Loading Inventory Type...')
                                ->afterStateUpdated(function ($state, callable $set, callable $get)
                                {
                                    if($state == null || $state == '')
                                    {
                                        $state = null;
                                        $set('transfer_from', null);
                                        $set('transfer_to', null);
                                    }
                                }),
                        ]),
                        Grid::make(2)
                        ->schema([
                                Select::make('transfer_from')
                                ->options(function (callable $get) {
                                    $inventory_type_id = $get('inventory_type_id');
                                    if (!$inventory_type_id) {
                                        return [];
                                    }
                                    else{
                                        $transferFrom = InventoryType::where('id', $inventory_type_id)->select('inventory_from')->get();

                                        if($transferFrom->first()->inventory_from == "WAREHOUSE")
                                        {
                                            return Warehouse::where('status', 'Active')->pluck('warehouse_name', 'id');
                                        }
                                        else{
                                            return Supplier::where('status', 'Active')->pluck('company_name', 'company_name');
                                        }
                                    }
                                    // return Province::where('region_code', $regionId)->pluck('province_name', 'province_code');
                                })
                                ->reactive() // Enables dynamic behavior
                                ->required()
                                ->searchable()
                                ->label('Transfer From')
                                ->loadingMessage('Loading Transfer From...'),

                                Select::make('transfer_to')
                                ->options(function (callable $get) {
                                    $inventory_type_id = $get('inventory_type_id');
                                    
                                    if (!$inventory_type_id) {
                                        return [];
                                    }
                                    else{
                                        $transferTo = InventoryType::where('id', $inventory_type_id)->select('inventory_to')->get();

                                        if($transferTo->first()->inventory_to == "WAREHOUSE")
                                        {
                                            return Warehouse::where('status', 'Active')->pluck('warehouse_name', 'id');
                                        }
                                        else{
                                            return Supplier::where('status', 'Active')->pluck('company_name', 'company_name');
                                        }
                                    }
                                   
                                    // return Province::where('region_code', $regionId)->pluck('province_name', 'province_code');
                                })
                                ->reactive() // Enables dynamic behavior
                                ->required()
                                ->searchable()
                                ->label('Transfer To')
                                ->loadingMessage('Loading Transfer To...'),
                        ]),
                        Grid::make(2)
                        ->schema([
                                Select::make('assigned_from')
                                ->options(Employee::select(
                                    DB::raw("CONCAT(first_name, ' ' ,last_name) AS name"), 'id')
                                    ->where('status', 'Active')
                                    ->pluck('name', 'name'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->label('Assigned From')
                                ->loadingMessage('Loading Assigned From...'),

                                Select::make('assigned_to')
                                ->options(Employee::select(
                                    DB::raw("CONCAT(first_name, ' ' ,last_name) AS name"), 'id')
                                    ->where('status', 'Active')
                                    ->pluck('name', 'name'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->label('Assigned To')
                                ->loadingMessage('Loading Assigned To...'),
                        ]),
                ])->columnSpanFull()

            ]);
    }
}
