<?php

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\Pages\ManageCustomers;
use App\Models\Customers;
use App\Models\Employee;
use App\Models\PriceCode;
use App\Models\Routes;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;
use App\Models\BusinessChannel;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\DB;
class CustomersResource extends Resource
{
    protected static ?string $model = Customers::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;
    protected static string | UnitEnum | null $navigationGroup = 'Customer Management';
    protected static ?string $navigationLabel = 'Customers';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('first_name')
                                    ->required()
                                    ->label('First Name')
                                    ->dehydrateStateUsing(function (?string $state): ?string {
                                        if ($state === null) {
                                            return '';
                                        }
                                            return strtoupper($state);
                                    }),
                                TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->dehydrateStateUsing(function (?string $state): ?string {
                                        if ($state === null) {
                                            return '';
                                        }
                                            return strtoupper($state);
                                    }),
                                TextInput::make('last_name')
                                    ->required()
                                    ->label('Last Name')
                                    ->dehydrateStateUsing(function (?string $state): ?string {
                                        if ($state === null) {
                                            return '';
                                        }
                                            return strtoupper($state);
                                    }),
                        ]),
                        Grid::make(3)
                            ->schema([
                                Select::make('employee_id')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Sales Representative')
                                    ->options(Employee::select(
                                        DB::raw("CONCAT(first_name, ' ' ,last_name) AS name"), 'id')
                                        ->where('status', 'Active')
                                        ->pluck('name', 'id'))
                                    ->loadingMessage('Loading Sales Representative...'),

                                TextInput::make('store_name')
                                    ->label('Store Name')
                                    ->required()
                                    ->dehydrateStateUsing(function (?string $state): ?string {
                                        if ($state === null) {
                                            return '';
                                        }
                                            return strtoupper($state);
                                    }),
                                
                                Select::make('business_channel_id')
                                    ->label('Business Channel')
                                    ->preload()
                                    ->options(BusinessChannel::where('status', 'Active')->pluck('business_channel_name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->loadingMessage('Loading Price Code...'),
                                
                            ]),
                        
                            Grid::make(2)
                            ->schema([
                                
                                Select::make('price_code_id')
                                    ->label('Price Code')
                                    ->preload()
                                    ->options(PriceCode::where('status', 'Active')->pluck('price_code', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->loadingMessage('Loading Price Code...'),
                                
                                Select::make('route_id')
                                    ->label('District (Route)')
                                    ->preload()
                                    ->options(Routes::where('status', 'Active')->pluck('route_name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->loadingMessage('Loading District (Route)...'),
                                
                            ]),

                            Grid::make(1)
                            ->schema([
                                TextInput::make('street_unit_building_no')
                                    ->label('Street/Unit/Building No.')
                                    ->dehydrateStateUsing(function (?string $state): ?string {
                                        if ($state === null) {
                                            return '';
                                        }
                                            return strtoupper($state);
                                    }),
                            ]),

                            Grid::make(2)
                            ->schema([
                                Select::make('region_code')
                                    ->label('Region')
                                    ->options(Region::all()->pluck('region_name', 'region_code'))
                                    ->searchable()
                                    ->required()
                                    ->loadingMessage('Loading Region...')
                                    ->reactive() // Enables dynamic behavior
                                    ->afterStateUpdated(function ($state, callable $set, callable $get)
                                    {
                                        if($state == null || $state == '')
                                        {
                                            $state = null;
                                            $set('province_code', null);
                                            $set('city_code', null);
                                            $set('barangay_code', null);
                                        }
                                    }),
                            
                                Select::make('province_code')
                                    ->label('Province')
                                    ->options(function (callable $get) {
                                        $regionId = $get('region_code');
                                        
                                        if (!$regionId) {
                                            return [];
                                        }
                                        
                                        return Province::where('region_code', $regionId)->pluck('province_name', 'province_code');
                                    })
                                    ->reactive() // Enables dynamic behavior
                                    ->searchable()
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get)
                                    {
                                        if($state == null || $state == '')
                                        {
                                            $state = null;
                                            $set('city_code', null);
                                            $set('brgy_code', null);
                                        }
                                      
                                    })
                                    ->loadingMessage('Loading Province...'),    

                                   
                            ]),
                            Grid::make(2)
                            ->schema([
                                Select::make('city_code')
                                ->label('City/Municipality')
                                ->options(function (callable $get) {
                                    $provinceId = $get('province_code');
                                    
                                    if (!$provinceId) {
                                        return [];
                                    }
                                    
                                    return City::where('province_code', $provinceId)->pluck('city_name', 'city_code');
                                })
                                ->searchable()
                                ->reactive() // Enables dynamic behavior
                                ->required()
                                ->afterStateUpdated(function ($state, callable $set, callable $get)
                                {

                                    if($state == null || $state == '')
                                    {
                                        $state = null;
                                        $set('brgy_code', null);
                                    }
                                  
                                })
                                ->loadingMessage('Loading City/Municipality...'),

                                Select::make('brgy_code')
                                ->label('Barangay')
                                ->options(function (callable $get) {
                                    $cityId = $get('city_code');
                                    
                                    if (!$cityId) {
                                        return [];
                                    }
                                    
                                    return Barangay::where('city_code', $cityId)->pluck('brgy_name', 'brgy_code');
                                })
                                ->searchable()
                                ->reactive() // Enables dynamic behavior
                                ->required()
                                ->loadingMessage('Loading Barangay...'),
                            ]),

                            Grid::make(2)
                            ->schema([
                                TextInput::make('contact_number')
                                    ->label('Contact No.')
                                    ->required()
                                    ->prefix('+63')
                                    ->maxLength(10)
                                    ->tel(),

                                Select::make('status')
                                    ->label('Status')
                                    ->required()
                                    ->options([
                                        'Active' => 'Active',
                                        'Inactive' => 'Inactive',
                                    ])
                                    ->searchable()
                            ]),

                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Customers')
            ->columns([
                TextColumn::make('store_name')
                    ->label('Store Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->getStateUsing(fn ($record) => $record->first_name . ' ' . $record->last_name),
                
                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                //DeleteAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCustomers::route('/'),
        ];
    }
}
