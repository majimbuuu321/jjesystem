<?php

namespace App\Filament\Resources\RouteGroups\Pages;

use App\Filament\Resources\RouteGroups\RouteGroupsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageRouteGroups extends ManageRecords
{
    protected static string $resource = RouteGroupsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false)
                ->label('Add Route Group')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
                }),
        ];
    }
}
