<?php

namespace App\Filament\Resources\InvoiceTypes\Pages;

use App\Filament\Resources\InvoiceTypes\InvoiceTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageInvoiceTypes extends ManageRecords
{
    protected static string $resource = InvoiceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->createAnother(false)
            ->label('Add Invoice Type')
            ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
            }),
        ];
    }
}
