<?php

namespace App\Filament\Resources\Invoices\Pages;
use App\Models\Employee;
use App\Models\Routes;
use App\Models\Customers;
use App\Models\Products;
use App\Models\ProductCategory;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Add Invoice'),
            Action::make('invoiceReport')
                ->label('Sales Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('danger')
                ->form([

                    Select::make('report_type')
                    ->label('Report Type')
                    ->searchable()
                    ->live()
                    ->options([
                        'sales_rep' => 'Sales per Sales Representative',
                        'route' => 'Sales per Route',
                        'customer' => 'Sales per Customer',
                        'category' => 'Sales per Product Category',
                        'product' => 'Sales per Product',
                    ])
                    ->required(),

                    Select::make('sales_representative')
                    ->label('Sales Rep')
                    ->multiple()
                    ->required()
                    ->options(
                        Employee::query()
                            ->orderBy('first_name')
                            ->get()
                            ->mapWithKeys(fn ($employee) => [
                                $employee->id => trim(
                                    $employee->first_name . ' ' . $employee->last_name
                                ),
                            ])
                            ->toArray()
                    )
                    ->visible(fn ($get) => $get('report_type') === 'sales_rep')
                    ->searchable()
                    ->preload(),

                    Select::make('route')
                        ->label('Route')
                        ->multiple()
                        ->options(
                            Routes::query()
                                ->orderBy('route_name')
                                ->pluck('route_name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(fn ($get) => $get('report_type') === 'route')
                        ->visible(fn ($get) => $get('report_type') === 'route'),

                    Select::make('customer')
                    ->label('Customer')
                    ->multiple()
                    ->options(
                        Customers::query()
                            ->orderBy('store_name')
                            ->get()
                            ->mapWithKeys(fn ($customer) => [
                                $customer->id => trim(
                                    ($customer->store_name ?? '') .
                                    ' - ' .
                                    ($customer->first_name ?? '') .
                                    ' ' .
                                    ($customer->last_name ?? '')
                                ),
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->required(fn ($get) => $get('report_type') === 'customer')
                    ->visible(fn ($get) => $get('report_type') === 'customer'),

                    Select::make('category')
                    ->label('Product Category')
                    ->multiple()
                    ->options(
                        ProductCategory::query()
                            ->orderBy('product_category_name')
                            ->pluck('product_category_name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->required(fn ($get) => $get('report_type') === 'category')
                    ->visible(fn ($get) => $get('report_type') === 'category'),

                    Select::make('product')
                    ->label('Product')
                    ->multiple()
                    ->options(
                        Products::query()
                            ->orderBy('product_description')
                            ->pluck('product_description', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->required(fn ($get) => $get('report_type') === 'product')
                    ->visible(fn ($get) => $get('report_type') === 'product'),

                    DatePicker::make('date_from')
                        ->label('Date From')
                        ->required()
                        ->default(now()->startOfMonth()),

                    DatePicker::make('date_to')
                        ->label('Date To')
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data) {

                    $dateFrom = Carbon::parse($data['date_from'])->startOfDay();
                    $dateTo   = Carbon::parse($data['date_to'])->endOfDay();

                    if ($dateFrom->gt($dateTo)) {
                        Notification::make()
                            ->title('Invalid Date Range')
                            ->body('Date From cannot be later than Date To.')
                            ->danger()
                            ->send();

                        return;
                    }

                    // $url = route('invoice.sales-report', [
                    //     'date_from' => $dateFrom->format('Y-m-d'),
                    //     'date_to'   => $dateTo->format('Y-m-d'),
                    // ]);

                     $url = match ($data['report_type']) {

                        'sales_rep' => route('invoice.sales-report', [
                            'date_from' => $data['date_from'],
                            'date_to' => $data['date_to'],
                            'sales_representative' => $data['sales_representative'],
                        ]),

                         'route' => route('invoice.route-report', [
                            'date_from' => $data['date_from'],
                            'date_to' => $data['date_to'],
                            'route' => $data['route'] ?? [],
                        ]),

                        'customer' => route('invoice.customer-report', [
                            'date_from' => $data['date_from'],
                            'date_to' => $data['date_to'],
                            'customer' => $data['customer'] ?? [],
                        ]),

                        'category' => route('invoice.category-report', [
                            'date_from' => $data['date_from'],
                            'date_to' => $data['date_to'],
                            'category' => $data['category'] ?? [],
                        ]),

                        'product' => route('invoice.product-report', [
                            'date_from' => $data['date_from'],
                            'date_to' => $data['date_to'],
                            'product' => $data['product'] ?? [],
                        ]),

                        default => null,
                     };

                    $this->js("window.open('{$url}', '_blank')");
                }),
        ];
    }
}
