<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Sales Per Category</title>

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

        .category-title {
            margin-top: 10px;
            padding: 6px;
            background: #ddd;
            border: 1px solid #aaa;
            font-weight: bold;
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

    <h2>SALES PER PRODUCT CATEGORY</h2>

    <div class="date">

        {{ $dateFrom->format('M d, Y') }}
        -
        {{ $dateTo->format('M d, Y') }}

    </div>

</div>


{{-- ========================================================= --}}
{{-- CASH --}}
{{-- ========================================================= --}}

@if($cashByCategory->count())

    <div class="section-title">
        CASH INVOICE
    </div>

    @foreach($cashByCategory as $categoryId => $details)

        @php
            $category = $details->first()->product?->category;
        @endphp

        <div class="category-title">

            CATEGORY:
            {{ $category?->product_category_name ?? 'No Category' }}

        </div>

        <table>

            <thead>

            <tr>

                <th>Product</th>
                <th>Invoice #</th>
                <th>Invoice Date</th>
                <th>Customer</th>
                <th>Sales Representative</th>
                <th>Terms</th>
                <th>Amount</th>

            </tr>

            </thead>

            <tbody>

            @foreach($details as $detail)

                @php
                    $invoice = $detail->report_invoice;
                @endphp

                <tr>

                    <td>
                        {{ $detail->product?->product_description ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $invoice->order_no ?? '-' }}
                    </td>

                    <td class="center">

                        {{ Carbon\Carbon::parse(
                            $invoice->invoice_date
                        )->format('m/d/Y') }}

                    </td>

                    <td>

                        {{ $invoice->customer?->store_name
                            ?? $invoice->customer?->customer_name
                            ?? '-' }}

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

                         {{ number_format($detail->net_amount ?? 0, 2) }}
                    </td>

                </tr>

            @endforeach

            <tr class="total">

                <td colspan="6" class="right">
                    CATEGORY TOTAL
                </td>

                <td class="right">

                     {{ number_format($cashTotal, 2) }}

                </td>

            </tr>

            </tbody>

        </table>

    @endforeach

@endif


{{-- ========================================================= --}}
{{-- CHARGE --}}
{{-- ========================================================= --}}

@if($chargeByCategory->count())

    <div class="section-title">
        CHARGE INVOICE
    </div>

    @foreach($chargeByCategory as $categoryId => $details)

        @php
            $category = $details->first()->product?->category;
        @endphp

        <div class="category-title">

            CATEGORY:
            {{ $category?->product_category_name ?? 'No Category' }}

        </div>

        <table>

            <thead>

            <tr>

                <th>Product</th>
                <th>Invoice #</th>
                <th>Invoice Date</th>
                <th>Customer</th>
                <th>Sales Representative</th>
                <th>Terms</th>
                <th>Amount</th>

            </tr>

            </thead>

            <tbody>

            @foreach($details as $detail)

                @php
                    $invoice = $detail->report_invoice;
                @endphp

                <tr>

                    <td>
                        {{ $detail->product?->product_description ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $invoice->order_no ?? '-' }}
                    </td>

                    <td class="center">

                        {{ Carbon\Carbon::parse(
                            $invoice->invoice_date
                        )->format('m/d/Y') }}

                    </td>

                    <td>

                        {{ $invoice->customer?->store_name
                            ?? $invoice->customer?->customer_name
                            ?? '-' }}

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

                         {{ number_format($detail->net_amount ?? 0, 2) }}

                    </td>

                </tr>

            @endforeach

            <tr class="total">

                <td colspan="6" class="right">
                    CATEGORY TOTAL
                </td>

                <td class="right">

                    {{ number_format($chargeTotal, 2) }}

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

</body>

</html>