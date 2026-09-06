<?php

namespace App\Filament\Resources\Routes\Pages;

use App\Filament\Resources\Routes\RoutesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageRoutes extends ManageRecords
{
    protected static string $resource = RoutesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
             ->createAnother(false)
            ->label('Add Routes')
            ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
                }),
        ];
    }
}
