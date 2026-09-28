<?php

namespace App\Filament\Resources\Products\Pages;
use App\Models\UnitCostHistory;
use App\Filament\Resources\Products\ProductsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProducts extends EditRecord
{
    protected static string $resource = ProductsResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         DeleteAction::make(),
    //     ];
    // }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function afterSave(): void
    {
        UnitCostHistory::create([
            'price_date' => $this->record->price_date,
            'products_id' => $this->record->id,
            'unit_cost' => $this->record->unit_cost,
            'created_by' => auth()->id(),
            'created_at' => now()
        ]);
    }
}
