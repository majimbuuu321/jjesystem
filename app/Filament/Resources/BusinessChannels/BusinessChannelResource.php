<?php

namespace App\Filament\Resources\BusinessChannels;

use App\Filament\Resources\BusinessChannels\Pages\ManageBusinessChannels;
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
use Filament\Forms\Components\Select;
class BusinessChannelResource extends Resource
{
    protected static ?string $model = BusinessChannel::class;
    protected static ?int $navigationSort = 5;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;
    protected static string | UnitEnum | null $navigationGroup = 'Product Management';
    protected static ?string $navigationLabel = 'Business Channel';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('business_channel_name')
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->label('Business Channel Name')
                    ->dehydrateStateUsing(function (?string $state): ?string {
                            if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            })
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ])
                    ->searchable(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('business_channel_name')
                    ->label('Business Channel Name')
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
            'index' => ManageBusinessChannels::route('/'),
        ];
    }
}
