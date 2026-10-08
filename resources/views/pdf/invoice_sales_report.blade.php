<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Sales Report</title>

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
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header h2 {
            margin: 4px 0;
            font-size: 13px;
        }

        .date-range {
            font-size: 10px;
            margin-top: 5px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 5px;
            padding: 6px;
            background: #eee;
            border: 1px solid #bbb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 5px;
            vertical-align: middle;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .amount {
            text-align: right;
        }

        .customer {
            line-height: 1.3;
        }

        .store {
            font-weight: bold;
        }

        .customer-name {
            font-size: 8px;
        }

        .total-row td {
            font-weight: bold;
            background: #f5f5f5;
        }

        .grand-total {
            margin-top: 15px;
            width: 45%;
            margin-left: auto;
        }

        .grand-total table td {
            font-size: 11px;
            font-weight: bold;
        }

        .grand-total .net-sale td {
            font-size: 13px;
        }

        .no-data {
            text-align: center;
            padding: 12px;
            border: 1px solid #aaa;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
        }

    </style>
</head>

<body>

    {{-- HEADER --}}

    <div class="header">

        <h1>JJE ENTERPRISES</h1>

        <h2>SALE PER SALES REPRESENTATIVE</h2>

        <div class="date-range">
            Date:
            {{ $dateFrom->format('M d, Y') }}
            -
            {{ $dateTo->format('M d, Y') }}
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CASH INVOICE --}}
    {{-- ========================================================= --}}

    @if ($cashInvoices->count() > 0)

        <div class="section-title">
            CASH INVOICE
        </div>

        <table>

            <thead>

                <tr>

                    <th width="22%">
                        CUSTOMER
                    </th>

                    <th width="10%">
                        INVOICE #
                    </th>

                    <th width="10%">
                        INVOICE DATE
                    </th>

                    <th width="15%">
                        SALES REPRESENTATIVE
                    </th>

                    <th width="13%">
                        TERMS
                    </th>

                    <th width="12%">
                        AMOUNT
                    </th>

                    <th width="8%">
                        PAID
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach ($cashInvoices as $invoice)

                    <tr>

                        <td class="customer">

                            <div class="store">
                                {{ $invoice->customer?->store_name
                                    ?? $invoice->customer?->customer_name
                                    ?? '-' }}
                            </div>

                            <div class="customer-name">

                                {{ trim(
                                    ($invoice->customer?->first_name ?? '') .
                                    ' ' .
                                    ($invoice->customer?->last_name ?? '')
                                ) }}

                            </div>

                        </td>

                        <td class="text-center">
                            {{ $invoice->order_no ?? $invoice->order_no ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ Carbon\Carbon::parse($invoice->invoice_date)->format('m/d/Y') }}
                        </td>

                        <td>
                            {{ $invoice->employee?->first_name }}
                            {{ $invoice->employee?->last_name }}
                        </td>

                        <td>
                            {{ $invoice->paymentTerms?->terms_name
                                ?? $invoice->paymentTerms?->payment_terms
                                ?? '-' }}
                        </td>

                        <td class="amount">
                            {{ number_format((float) ($invoice->total_amount ?? 0), 2) }}
                        </td>

                        <td class="amount">
                            {{ number_format((float) ($invoice->paid_amount ?? 0), 2) }}
                        </td>

                    </tr>

                @endforeach

                <tr class="total-row">

                    <td colspan="6" class="amount">
                        TOTAL CASH SALES
                    </td>

                    <td class="amount">
                        {{ number_format($cashTotal, 2) }}
                    </td>

                </tr>

            </tbody>

        </table>

    @endif


    {{-- ========================================================= --}}
    {{-- CHARGE INVOICE --}}
    {{-- ========================================================= --}}

    @if ($chargeInvoices->count() > 0)

        <div class="section-title">
            CHARGE INVOICE
        </div>

        <table>

            <thead>

                <tr>

                    <th width="22%">
                        CUSTOMER
                    </th>

                    <th width="10%">
                        INVOICE #
                    </th>

                    <th width="10%">
                        INVOICE DATE
                    </th>

                    <th width="15%">
                        SALES REPRESENTATIVE
                    </th>

                    <th width="13%">
                        TERMS
                    </th>

                    <th width="12%">
                        AMOUNT
                    </th>

                    <th width="8%">
                        PAID
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach ($chargeInvoices as $invoice)

                    <tr>

                        <td class="customer">

                            <div class="store">
                                {{ $invoice->customer?->store_name
                                    ?? $invoice->customer?->customer_name
                                    ?? '-' }}
                            </div>

                            <div class="customer-name">

                                {{ trim(
                                    ($invoice->customer?->first_name ?? '') .
                                    ' ' .
                                    ($invoice->customer?->last_name ?? '')
                                ) }}

                            </div>

                        </td>

                        <td class="text-center">
                            {{ $invoice->order_no ?? $invoice->order_no ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ Carbon\Carbon::parse($invoice->invoice_date)->format('m/d/Y') }}
                        </td>

                        <td>
                            {{ $invoice->employee?->first_name }}
                            {{ $invoice->employee?->last_name }}
                        </td>

                        <td>
                            {{ $invoice->paymentTerms?->terms_name
                                ?? $invoice->paymentTerms?->payment_terms
                                ?? '-' }}
                        </td>

                        <td class="amount">
                            {{ number_format((float) ($invoice->total_amount ?? 0), 2) }}
                        </td>

                        <td class="amount">
                            {{ number_format((float) ($invoice->paid_amount ?? 0), 2) }}
                        </td>

                    </tr>

                @endforeach

                <tr class="total-row">

                    <td colspan="6" class="amount">
                        TOTAL CHARGE SALES
                    </td>

                    <td class="amount">
                        {{ number_format($chargeTotal, 2) }}
                    </td>

                </tr>

            </tbody>

        </table>

    @endif


    {{-- ========================================================= --}}
    {{-- NO DATA --}}
    {{-- ========================================================= --}}

    @if ($cashInvoices->count() === 0 && $chargeInvoices->count() === 0)

        <div class="no-data">
            No posted invoices found for the selected date range.
        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- GRAND TOTAL --}}
    {{-- ========================================================= --}}

    @if ($cashInvoices->count() > 0 || $chargeInvoices->count() > 0)

        <div class="grand-total">

            <table>

                <tr>
                    <td>
                        Total Cash Sales
                    </td>

                    <td class="amount">
                        {{ number_format($cashTotal, 2) }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Total Charge Sales
                    </td>

                    <td class="amount">
                        {{ number_format($chargeTotal, 2) }}
                    </td>
                </tr>

                <tr class="net-sale">

                    <td>
                        TOTAL NET SALE
                    </td>

                    <td class="amount">
                        {{ number_format($netSale, 2) }}
                    </td>

                </tr>
            </table>

        </div>

    @endif


    <div class="footer">

        Generated:
        {{ now()->format('M d, Y h:i A') }}

    </div>

    <script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
</body>

</html>