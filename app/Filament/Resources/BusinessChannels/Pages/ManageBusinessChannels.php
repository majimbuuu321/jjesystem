<?php

namespace App\Filament\Resources\BusinessChannels\Pages;

use App\Filament\Resources\BusinessChannels\BusinessChannelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBusinessChannels extends ManageRecords
{
    protected static string $resource = BusinessChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->createAnother(false)
            ->label('Add Business Channel')
            ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
                }),
        ];
    }
}
