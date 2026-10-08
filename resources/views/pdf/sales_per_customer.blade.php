<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Sales Per Customer</title>

    <style>

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header h2 {
            margin: 4px 0;
            font-size: 13px;
        }

        .date {
            margin-top: 5px;
        }

        .section-title {
            margin-top: 15px;
            padding: 6px;
            background: #eee;
            border: 1px solid #aaa;
            font-size: 12px;
            font-weight: bold;
        }

        .customer-title {
            margin-top: 10px;
            padding: 6px;
            background: #ddd;
            border: 1px solid #aaa;
            font-weight: bold;
        }

        .customer-name {
            font-size: 9px;
            font-weight: normal;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 5px;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .total {
            font-weight: bold;
            background: #f5f5f5;
        }

        .grand-total {
            margin-top: 20px;
            width: 40%;
            margin-left: auto;
        }

        .grand-total td {
            font-size: 11px;
            font-weight: bold;
        }

        .net-sale td {
            font-size: 13px;
        }

    </style>

</head>

<body>

<div class="header">

    <h1>JJE ENTERPRISES</h1>

    <h2>SALES PER CUSTOMER</h2>

    <div class="date">

        {{ $dateFrom->format('M d, Y') }}
        -
        {{ $dateTo->format('M d, Y') }}

    </div>

</div>


{{-- ========================================================= --}}
{{-- CASH INVOICE --}}
{{-- ========================================================= --}}

@if($cashInvoices->count())

    <div class="section-title">
        CASH INVOICE
    </div>

    @foreach($cashByCustomer as $customerId => $invoices)

        @php
            $customer = $invoices->first()->customer;
        @endphp

        <div class="customer-title">

            {{ $customer?->store_name
                ?? $customer?->customer_name
                ?? 'No Customer' }}

            <div class="customer-name">

                {{ trim(
                    ($customer?->first_name ?? '') .
                    ' ' .
                    ($customer?->last_name ?? '')
                ) }}

            </div>

        </div>

        <table>

            <thead>

            <tr>

                <th>Invoice #</th>

                <th>Invoice Date</th>

                <th>Sales Representative</th>

                <th>Terms</th>

                <th>Amount</th>

            </tr>

            </thead>

            <tbody>

            @foreach($invoices as $invoice)

                <tr>

                    <td class="center">
                        {{ $invoice->order_no ?? '-' }}
                    </td>

                    <td class="center">

                        {{ Carbon\Carbon::parse(
                            $invoice->invoice_date
                        )->format('m/d/Y') }}

                    </td>

                    <td>

                        {{ $invoice->employee?->first_name }}
                        {{ $invoice->employee?->last_name }}

                    </td>

                    <td>

                        {{ $invoice->paymentTerms?->payment_terms
                            ?? '-' }}

                    </td>

                    <td class="right">

                        {{ number_format(
                            $invoice->paid_amount ?? 0,
                            2
                        ) }}

                    </td>

                </tr>

            @endforeach

            <tr class="total">

                <td colspan="4" class="right">
                    CUSTOMER CASH TOTAL
                </td>

                <td class="right">

                    {{ number_format(
                        $invoices->sum('paid_amount'),
                        2
                    ) }}

                </td>

            </tr>

            </tbody>

        </table>

    @endforeach

@endif


{{-- ========================================================= --}}
{{-- CHARGE INVOICE --}}
{{-- ========================================================= --}}

@if($chargeInvoices->count())

    <div class="section-title">
        CHARGE INVOICE
    </div>

    @foreach($chargeByCustomer as $customerId => $invoices)

        @php
            $customer = $invoices->first()->customer;
        @endphp

        <div class="customer-title">

            {{ $customer?->store_name
                ?? $customer?->customer_name
                ?? 'No Customer' }}

            <div class="customer-name">

                {{ trim(
                    ($customer?->first_name ?? '') .
                    ' ' .
                    ($customer?->last_name ?? '')
                ) }}

            </div>

        </div>

        <table>

            <thead>

            <tr>

                <th>Invoice #</th>

                <th>Invoice Date</th>

                <th>Sales Representative</th>

                <th>Terms</th>

                <th>Amount</th>

            </tr>

            </thead>

            <tbody>

            @foreach($invoices as $invoice)

                <tr>

                    <td class="center">
                        {{ $invoice->order_no ?? '-' }}
                    </td>

                    <td class="center">

                        {{ Carbon\Carbon::parse(
                            $invoice->invoice_date
                        )->format('m/d/Y') }}

                    </td>

                    <td>

                        {{ $invoice->employee?->first_name }}
                        {{ $invoice->employee?->last_name }}

                    </td>

                    <td>

                        {{ $invoice->paymentTerms?->payment_terms
                            ?? '-' }}

                    </td>

                    <td class="right">

                        {{ number_format(
                            $invoice->paid_amount ?? 0,
                            2
                        ) }}

                    </td>

                </tr>

            @endforeach

            <tr class="total">

                <td colspan="4" class="right">
                    CUSTOMER CHARGE TOTAL
                </td>

                <td class="right">

                    {{ number_format(
                        $invoices->sum('paid_amount'),
                        2
                    ) }}

                </td>

            </tr>

            </tbody>

        </table>

    @endforeach

@endif


{{-- ========================================================= --}}
{{-- GRAND TOTAL --}}
{{-- ========================================================= --}}

<div class="grand-total">

    <table>

        <tr>

            <td>
                Total Cash Sales
            </td>

            <td class="right">
                {{ number_format($cashTotal, 2) }}
            </td>

        </tr>

        <tr>

            <td>
                Total Charge Sales
            </td>

            <td class="right">
                {{ number_format($chargeTotal, 2) }}
            </td>

        </tr>

        <tr class="net-sale">

            <td>
                TOTAL NET SALE
            </td>

            <td class="right">
                {{ number_format($netSale, 2) }}
            </td>

        </tr>

    </table>

</div>
 <script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
</body>

</html>