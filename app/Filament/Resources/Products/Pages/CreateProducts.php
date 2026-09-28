<?php

namespace App\Filament\Resources\Products\Pages;
use App\Models\UnitCostHistory;
use App\Models\PriceCode;
use App\Models\PricePerCode;
use App\Filament\Resources\Products\ProductsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProducts extends CreateRecord
{
    protected static string $resource = ProductsResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {

        UnitCostHistory::create([
            'price_date' => $this->record->price_date,
            'products_id' => $this->record->id,
            'unit_cost' => $this->record->unit_cost,
            'created_by' => auth()->id(),
            'created_at' => now()
        ]);

        $priceCodes = PriceCode::where('status', 'Active')->get();
        foreach ($priceCodes as $priceCode) {
            PricePerCode::create([
                'products_id' => $this->record->id,
                'price_code_id' => $priceCode->id,
                'status' => 'Active',
                'created_by' => auth()->id(),
                'created_at' => now()
            ]);
        }
        
    }
}


