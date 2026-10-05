<?php

namespace App\Filament\Resources\Inventories\Pages;
use App\Models\InventoryHeader;
use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('inventoryReport')
            ->label('Inventory Report')
            ->icon('heroicon-o-document-chart-bar')
            ->color('danger')
            ->form([
                DatePicker::make('date_from')
                    ->label('Date From')
                    ->required()
                    ->default(now()->startOfMonth()),

                DatePicker::make('date_to')
                    ->label('Date To')
                    ->required()
                    ->default(now()),

                Select::make('status')
                ->label('Status')
                ->options([
                    'All' => 'All',
                    'Posted' => 'Posted',
                    'Draft' => 'Draft',
                ])
                ->default('All')
                ->required(),
            ])
            ->action(function (array $data) {

                $url = route('reports.inventory', [
                    'date_from' => $data['date_from'],
                    'date_to' => $data['date_to'],
                    'status' => $data['status'],
                ]);

                $this->js(
                    "window.open(" . json_encode($url) . ", '_blank');"
                );
            })
            
        ];
    }
}
