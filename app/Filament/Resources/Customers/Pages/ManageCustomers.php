<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomersResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCustomers extends ManageRecords
{
    protected static string $resource = CustomersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->createAnother(false)
            ->label('Add Customer')
            ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
            }),
        ];
    }
}
