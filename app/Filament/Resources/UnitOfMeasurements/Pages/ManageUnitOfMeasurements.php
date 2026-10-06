<?php

namespace App\Filament\Resources\UnitOfMeasurements\Pages;

use App\Filament\Resources\UnitOfMeasurements\UnitOfMeasurementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUnitOfMeasurements extends ManageRecords
{
    protected static string $resource = UnitOfMeasurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->createAnother(false)
            ->label('Add Warehouse')
            ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
            }),
        ];
    }
}
