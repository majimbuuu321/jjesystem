<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;

use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('post')
            ->label('Post Purchase Order')
            ->color('danger')
            ->icon('heroicon-m-check-circle')
            ->requiresConfirmation() 
            ->modalHeading('Post Purchase Order')
            ->modalDescription('Are you sure you want to post this? This action cannot be undone.')
            ->action(function () {
                $this->record->update(['status' => 'Posted']);
                
                $this->getSavedNotification()?->send();
                return redirect($this->getResource()::getUrl('index'));
            }),
        ];
    }
}
