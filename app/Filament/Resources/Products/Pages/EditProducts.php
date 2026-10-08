<?php

namespace App\Filament\Resources\Products\Pages;
use App\Models\UnitCostHistory;
use App\Models\PricePerCode;
use App\Models\PriceCode;
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

        $latestHistory = UnitCostHistory::where('products_id', $this->record->id)
            ->orderByDesc('price_date')
            ->orderByDesc('id')
            ->first();

        $priceDate = $this->record->price_date;
        $unitCost = (float) $this->record->unit_cost;

        $shouldCreate = !$latestHistory
            || $latestHistory->price_date != $priceDate
            || (float) $latestHistory->unit_cost != $unitCost;

        if ($shouldCreate) {
            UnitCostHistory::create([
                'price_date' => $priceDate,
                'products_id' => $this->record->id,
                'unit_cost' => $unitCost,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);
        }

        $cogsPriceCodeId = PriceCode::where('price_code', 'COGS')
            ->value('id');

        PricePerCode::where('products_id', $this->record->id)
            ->where('price_code_id', $cogsPriceCodeId)
            ->update([
                'unit_price' => $this->record->unit_cost,
            ]);

        $this->redirect(static::getUrl(['record' => $this->record]));
    }
}
