<?php

namespace App\Filament\Resources\Invoices\Schemas;
use App\Models\InvoiceHeader;
use App\Models\Employee;
use App\Models\Customers;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Invoice Information')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('transaction_no')
                                ->label('Transaction No.')
                                ->default(function () {
                                    $year = now()->year;
                                    $prefix = "INV-{$year}-";

                                    $lastInvoice = InvoiceHeader::where('transaction_no', 'like', "{$prefix}%")
                                        ->get()
                                        ->map(function ($invoice) use ($prefix) {
                                            return (int) str_replace($prefix, '', $invoice->order_no);
                                        })
                                        ->max();

                                    $counter = ($lastInvoice ?? 0) + 1;

                                    return "{$prefix}{$counter}";
                                })
                                ->readOnly()
                                ->required(),
                            TextInput::make('order_no')
                                ->label('Order No.')
                                ->unique(ignoreRecord: true)
                                ->required(),


                            DatePicker::make('invoice_date')
                                ->required()
                                ->label('Invoice Date')
                                ->minDate(now()->subYears(150))
                                ->maxDate(now())
                                ->default(now()),

                             Select::make('invoice_type_id')
                                    ->relationship('invoiceType', 'invoice_type_name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Invoice Type')
                                    ->loadingMessage('Loading Invoice Type...'),

                            Select::make('warehouse_id')
                                    ->relationship('warehouse', 'warehouse_name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Delivery From')
                                    ->loadingMessage('Loading Warehouses...'),

                                

                             Select::make('customer_id')
                                ->options(Customers::select(
                                    DB::raw("CONCAT(first_name, ' ', last_name, ' - ', store_name) AS name"), 'id')
                                    ->where('status', 'Active')
                                    ->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->label('Customer')
                                ->loadingMessage('Loading Customer...')
                                ->afterStateUpdated(function ($state, Set $set) {
                                        $customer = Customers::find($state);

                                        $set(
                                            'price_code_id',
                                            $customer?->price_code_id
                                        );
                                    }),

                            Hidden::make('price_code_id')
                                ->dehydrated(),

                            Select::make('employee_id')
                                ->options(Employee::select(
                                    DB::raw("CONCAT(first_name, ' ' ,last_name) AS name"), 'id')
                                    ->where('status', 'Active')
                                    ->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->label('Sales Representative')
                                ->loadingMessage('Loading Sales Representative...'),

                           Select::make('route_id')
                                    ->relationship('route', 'route_name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Route')
                                    ->loadingMessage('Loading Route...'),


                            Select::make('payment_terms_id')
                                    ->relationship('paymentTerms', 'payment_terms')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Payment Terms')
                                    ->loadingMessage('Loading Payment Terms...'),

                            // Select::make('status')
                            // ->label('Status')
                            // ->searchable()
                            // ->options([
                            //     'Partially Paid' => 'Partially Paid',
                            //     'Paid' => 'Paid',
                            //     'Cancelled' => 'Cancelled',
                            // ])
                            // ->hidden(function (callable $get) {
                            //     return in_array($get('status'), ['Draft']);
                            // })

                        ]),
                ])->columnSpanFull(),
            ]);
    }
}
