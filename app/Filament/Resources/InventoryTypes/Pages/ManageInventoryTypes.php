<?php

namespace App\Filament\Resources\InventoryTypes\Pages;

use App\Filament\Resources\InventoryTypes\InventoryTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageInventoryTypes extends ManageRecords
{
    protected static string $resource = InventoryTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Add Inventory Type')
            ->createAnother(false)
            ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
            }),
        ];
    }
}
