<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\ManageEmployees;
use App\Models\Employee;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;
    protected static string | UnitEnum | null $navigationGroup = 'Employee Management';
    protected static ?string $navigationLabel = 'Employee';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('employee_code')
                ->columnSpanFull()
                ->label('Employee Code')
                ->unique(ignoreRecord:true)                
                ->required()
                ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),

                // Row 2: The fields automatically split into 3 equal parts side-by-side
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
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
                    ->label('Last Name')
                    ->required()
                    ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                
                TextArea::make('address')
                            ->required()
                            ->columnSpanFull()
                            ->label('Address')
                            ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                
                Select::make('gender')
                    ->label('Gender')
                    ->required()
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])
                    ->searchable(),
                
                TextInput::make('contact_number')
                ->label('Contact Number')
                ->tel()
                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),

                DatePicker::make('birth_date')
                ->label('Date of Birth')
                ->required()
                ->minDate(now()->subYears(150))
                ->maxDate(now()),
               
                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ])
                    ->searchable()
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_code')
                    ->label('Employee Code')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->label('Middle Name'),
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),
               TextColumn::make('status')
                ->badge()
                ->label('Status')
                ->color(fn (string $state): string => match ($state) {
                    'Active' => 'success',
                    'Inactive' => 'danger',
                }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['updated_by'] = auth()->id();
                    return $data;
                }),
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
            'index' => ManageEmployees::route('/'),
        ];
    }
}
