<?php

namespace App\Filament\Resources\WarehouseTypes\Pages;

use App\Filament\Resources\WarehouseTypes\WarehouseTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWarehouseTypes extends ManageRecords
{
    protected static string $resource = WarehouseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
             ->createAnother(false)
                ->label('Add Warehouse Type')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
                }),
        ];
    }
}
