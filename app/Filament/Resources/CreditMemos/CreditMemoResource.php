<?php

namespace App\Filament\Resources\CreditMemos;

use App\Filament\Resources\CreditMemos\Pages\CreateCreditMemo;
use App\Filament\Resources\CreditMemos\Pages\EditCreditMemo;
use App\Filament\Resources\CreditMemos\Pages\ListCreditMemos;
use App\Filament\Resources\CreditMemos\Schemas\CreditMemoForm;
use App\Filament\Resources\CreditMemos\Tables\CreditMemosTable;
use App\Models\CreditMemoHeader;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
class CreditMemoResource extends Resource
{
    protected static ?string $model = CreditMemoHeader::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentMinus;
    protected static string | UnitEnum | null $navigationGroup = 'Credit Memo';
    protected static ?string $navigationLabel = 'Credit Memos';
    protected static ?string $modelLabel = 'Credit Memo';
    public static function form(Schema $schema): Schema
    {
        return CreditMemoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CreditMemosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\DetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCreditMemos::route('/'),
            'create' => CreateCreditMemo::route('/create'),
            'edit' => EditCreditMemo::route('/{record}/edit'),
        ];
    }
}
