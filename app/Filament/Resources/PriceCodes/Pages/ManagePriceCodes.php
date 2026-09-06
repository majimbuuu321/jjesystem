<?php

namespace App\Filament\Resources\PriceCodes\Pages;

use App\Filament\Resources\PriceCodes\PriceCodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePriceCodes extends ManageRecords
{
    protected static string $resource = PriceCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
