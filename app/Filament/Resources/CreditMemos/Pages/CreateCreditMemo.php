<?php

namespace App\Filament\Resources\CreditMemos\Pages;

use App\Filament\Resources\CreditMemos\CreditMemoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCreditMemo extends CreateRecord
{
    protected static string $resource = CreditMemoResource::class;
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
