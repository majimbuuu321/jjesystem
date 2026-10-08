<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>
        Invoice {{ $invoice->transaction_no ?? $invoice->id }}
    </title>

    <style>
        @page {
        size: A4;
        margin: 12mm 18mm 15mm 18mm;
        }

        @media print {
            html,
            body {
                margin: 0;
                padding: 0;
            };
            * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 100%;
        }

        /* =========================
           COLORS
        ========================== */

        .brand {
            color: #381757;
        }

        .brand-bg {
            background-color: #381757;
            color: #ffffff;
        }

        .light-bg {
            background-color: #f7f4fa;
        }

        .border {
            border-color: #d9d2df;
        }

        /* =========================
           HEADER
        ========================== */

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .header-left {
            width: 65%;
            vertical-align: middle;
        }

        .header-right {
            width: 35%;
            text-align: right;
            vertical-align: middle;
        }

        .company-name {
            font-size: 21px;
            font-weight: bold;
            color: #381757;
            margin-bottom: 4px;
        }

        .company-subtitle {
            font-size: 10px;
            color: #777;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #381757;
            letter-spacing: 1px;
        }

        .invoice-number {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        /* =========================
           TOP LINE
        ========================== */

        .header-line {
            height: 3px;
            background-color: #381757;
            margin-bottom: 18px;
        }

        /* =========================
           INFORMATION BOXES
        ========================== */

        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-left: -8px;
            margin-right: -8px;
            margin-bottom: 15px;
        }

        .info-box {
            border: 1px solid #d9d2df;
            padding: 10px;
            vertical-align: top;
            background: #ffffff;
        }

        .info-title {
            font-size: 10px;
            font-weight: bold;
            color: #381757;
            text-transform: uppercase;
            margin-bottom: 7px;
            letter-spacing: 0.5px;
        }

        .info-label {
            color: #777;
            font-size: 9px;
        }

        .info-value {
            font-size: 10px;
            font-weight: bold;
            color: #222;
        }

        .info-row {
            padding-bottom: 4px;
        }

        /* =========================
           STATUS
        ========================== */

        .status {
            display: inline-block;
            padding: 4px 9px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 10px;
            background-color: #381757;
            color: #ffffff;
        }

        /* =========================
           ITEMS TABLE
        ========================== */

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items th {
            background-color: #381757;
            color: #ffffff;
            font-size: 9px;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #381757;
        }

        .items td {
            border: 1px solid #ddd;
            padding: 7px 6px;
            font-size: 9px;
            vertical-align: middle;
        }

        .items tbody tr:nth-child(even) {
            background-color: #faf9fb;
        }

        .product-code {
            font-weight: bold;
            color: #381757;
        }

        .description {
            font-size: 9px;
        }

        /* =========================
           TOTALS
        ========================== */

        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .bottom-left {
            width: 55%;
            vertical-align: top;
            padding-right: 15px;
        }

        .bottom-right {
            width: 45%;
            vertical-align: top;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e5e5;
        }

        .summary .label {
            color: #555;
        }

        .summary .value {
            text-align: right;
            font-weight: bold;
        }

        .summary .grand-total td {
            background-color: #381757;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            padding: 9px 8px;
        }

        .summary .balance td {
            background-color: #f7f4fa;
            color: #381757;
            font-size: 12px;
            font-weight: bold;
            border-bottom: 2px solid #381757;
        }

        /* =========================
           PAYMENT HISTORY
        ========================== */

        .section-title {
            margin-top: 22px;
            margin-bottom: 7px;
            font-size: 11px;
            font-weight: bold;
            color: #381757;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payments {
            width: 100%;
            border-collapse: collapse;
        }

        .payments th {
            background-color: #f1edf4;
            color: #381757;
            font-size: 9px;
            font-weight: bold;
            padding: 7px;
            border: 1px solid #d9d2df;
        }

        .payments td {
            font-size: 9px;
            padding: 7px;
            border: 1px solid #ddd;
        }

        .no-payment {
            text-align: center;
            color: #777;
            font-style: italic;
        }

        /* =========================
           NOTES
        ========================== */

        .notes-box {
            border: 1px solid #d9d2df;
            padding: 10px;
            margin-top: 15px;
            min-height: 45px;
        }

        .notes-title {
            font-weight: bold;
            color: #381757;
            font-size: 9px;
            margin-bottom: 5px;
        }

        .notes-text {
            font-size: 9px;
            color: #666;
        }

        /* =========================
           SIGNATURES
        ========================== */

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 55px;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 5px 15px;
        }

        .signature-line {
            border-top: 1px solid #444;
            margin-top: 35px;
            padding-top: 5px;
            font-size: 9px;
            color: #555;
        }

        /* =========================
           FOOTER
        ========================== */

        .footer {
            margin-top: 25px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #888;
            font-size: 8px;
        }

        .footer-brand {
            color: #381757;
            font-weight: bold;
        }

        /* =========================
           UTILITIES
        ========================== */

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <table class="header-table">

        <tr>

            <td class="header-left">

                <div class="company-name">
                     JJE ENTERPRISES
                </div>

                <div class="company-subtitle">
                    456 Caggay Maharlika Highway, Tuguegarao City, Cagayan  - Tel. No. (078) 304-9210
                </div>

            </td>

            <td class="header-right">

                <div class="invoice-title">
                    INVOICE
                </div>

                <div class="invoice-number">
                    #{{ $invoice->transaction_no ?? $invoice->id }}
                </div>

            </td>

        </tr>

    </table>

    <div class="header-line"></div>


    {{-- =========================================================
         CUSTOMER / INVOICE INFORMATION
    ========================================================== --}}

    <table class="info-table">

        <tr>

            {{-- CUSTOMER --}}
            <td class="info-box" width="50%">

                <div class="info-title">
                    Bill To
                </div>

                <div class="info-row">

                    <div class="info-label">
                        Customer
                    </div>

                    <div class="info-value">

                        {{ $invoice->customer
                            ? trim(
                                ($invoice->customer->store_name
                                    ? $invoice->customer->store_name . ' - '
                                    : ''
                                ) .
                                ($invoice->customer->first_name ?? '') . ' ' .
                                ($invoice->customer->last_name ?? '')
                            )
                            : 'N/A'
                        }}

                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">
                        Route
                    </div>

                    <div class="info-value">
                        {{ $invoice->route?->route_name ?? 'N/A' }}
                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">
                        Payment Terms
                    </div>

                    <div class="info-value">
                        {{ $invoice->paymentTerms?->payment_terms ?? 'N/A' }}
                    </div>

                </div>

            </td>


            {{-- INVOICE INFORMATION --}}
            <td class="info-box" width="50%">

                <div class="info-title">
                    Invoice Information
                </div>

                <table width="100%">

                    <tr>
                        <td class="info-label">
                            Invoice Date
                        </td>

                        <td class="info-value text-right">

                            {{ $invoice->invoice_date
                                ? \Carbon\Carbon::parse(
                                    $invoice->invoice_date
                                )->format('M d, Y')
                                : 'N/A'
                            }}

                        </td>
                    </tr>

                    <tr>
                        <td class="info-label">
                            Order No.
                        </td>

                        <td class="info-value text-right">
                            {{ $invoice->order_no ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="info-label">
                            Warehouse
                        </td>

                        <td class="info-value text-right">
                            {{ $invoice->warehouse?->warehouse_name ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="info-label">
                            Invoice Type
                        </td>

                        <td class="info-value text-right">
                            {{ $invoice->invoiceType?->invoice_type_name ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="info-label">
                            Sales Representative
                        </td>

                        <td class="info-value text-right">
                           {{ $invoice->employee
                                ? trim(
                                    ($invoice->employee->first_name ?? '') . ' ' .
                                    ($invoice->employee->last_name ?? '')
                                )
                                : 'N/A'
                            }}
                        </td>
                    </tr>

                    <tr>
                        <td class="info-label">
                            Status
                        </td>

                        <td class="text-right">

                            <span class="status">
                                {{ strtoupper($invoice->status ?? 'N/A') }}
                            </span>

                        </td>
                    </tr>

                </table>

            </td>

        </tr>

    </table>
    


    {{-- =========================================================
         PRODUCT DETAILS
    ========================================================== --}}

    <div class="section-title">
        Invoice Details
    </div>

    <table class="items">

        <thead>

            <tr>

                <th width="11%">
                    Product Code
                </th>

                <th width="27%">
                    Description
                </th>

                <th width="9%">
                    UOM
                </th>

                <th width="8%">
                    Qty
                </th>

                <th width="8%">
                    Weight (kg)
                </th>

                <th width="12%">
                    Unit Price
                </th>

                <th width="12%">
                    Gross
                </th>

                <th width="10%">
                    Discount
                </th>

                <th width="13%">
                    Net Amount
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($invoice->InvoiceDetails as $detail)

                <tr>

                    <td class="product-code text-center">

                        {{ $detail->product?->product_code ?? 'N/A' }}

                    </td>

                    <td class="description">

                        {{ $detail->product?->product_description
                            ?? $detail->product?->product_name
                            ?? 'N/A'
                        }}

                    </td>

                    <td class="text-center">

                        {{ $detail->units?->unit_code ?? 'N/A' }}

                    </td>

                    <td class="text-right">

                        {{ number_format(
                            (float) $detail->quantity,
                            2
                        ) }}

                    </td>

                    <td class="text-right">

                        {{ number_format(
                            (float) $detail->net_weight,
                            2
                        ) }}

                    </td>

                    <td class="text-right">

                        {{ number_format(
                            (float) $detail->price,
                            2
                        ) }}

                    </td>

                    <td class="text-right">

                        {{ number_format(
                            (float) $detail->gross_amount,
                            2
                        ) }}

                    </td>

                    <td class="text-right">

                        {{ number_format(
                            (float) $detail->discount_amount,
                            2
                        ) }}

                    </td>

                    <td class="text-right bold">

                        {{ number_format(
                            (float) $detail->net_amount,
                            2
                        ) }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="8" class="text-center">
                        No invoice items found.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- =========================================================
         TOTALS
    ========================================================== --}}

    <table class="bottom-table">

        <tr>

            <td class="bottom-left">

                <div class="notes-box">

                    <div class="notes-title">
                        Invoice Summary
                    </div>

                    <div class="notes-text">

                        Total Product Lines:
                        <strong>
                            {{ $invoice->InvoiceDetails->count() }}
                        </strong>

                        <br>

                        Total Quantity:
                        <strong>
                            {{ number_format(
                                $invoice->InvoiceDetails->sum('quantity'),
                                2
                            ) }}
                        </strong>
                         <br>
                        Total Weight:
                        <strong>
                            {{ number_format(
                                $invoice->InvoiceDetails->sum('net_weight'),
                                2
                            ) }}
                        </strong>

                    </div>

                </div>

            </td>


            <td class="bottom-right">

                <table class="summary">

                    {{-- GROSS --}}
                    <tr>

                        <td class="label">
                            Gross Total
                        </td>

                        <td class="value">

                            {{ number_format(
                                $invoice->InvoiceDetails->sum('gross_amount'),
                                2
                            ) }}

                        </td>

                    </tr>


                    {{-- DISCOUNT --}}
                    <tr>

                        <td class="label">
                            Discount
                        </td>

                        <td class="value">

                            {{ number_format(
                                $invoice->InvoiceDetails->sum('discount_amount'),
                                2
                            ) }}

                        </td>

                    </tr>


                    {{-- TOTAL --}}
                    <tr class="grand-total">

                        <td>
                            TOTAL AMOUNT
                        </td>

                        <td class="text-right">

                            {{ number_format(
                                (float) $invoice->total_amount,
                                2
                            ) }}

                        </td>

                    </tr>


                    {{-- PAID --}}
                    <tr>

                        <td class="label">
                            Paid Amount
                        </td>

                        <td class="value">

                            {{ number_format(
                                (float) $invoice->paid_amount,
                                2
                            ) }}

                        </td>

                    </tr>


                    {{-- BALANCE --}}
                    <tr class="balance">

                        <td>
                            BALANCE
                        </td>

                        <td class="text-right">

                            {{ number_format(
                                (float) $invoice->balance_amount,
                                2
                            ) }}

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>


    {{-- =========================================================
         PAYMENT HISTORY
    ========================================================== --}}

    <div class="section-title">
        Payment History
    </div>

    <table class="payments">

        <thead>

            <tr>

                <th width="20%">
                    Date
                </th>

                <th width="25%">
                    Payment
                </th>

                <th width="25%">
                    Mode of Payment
                </th>

                <th width="30%">
                    Reference
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($invoice->payments as $payment)

                <tr>

                    <td>

                        {{ $payment->payment_date
                            ? \Carbon\Carbon::parse(
                                $payment->payment_date
                            )->format('M d, Y')
                            : 'N/A'
                        }}

                    </td>

                    <td class="text-right">

                        {{ number_format(
                            (float) $payment->payment_amount,
                            2
                        ) }}

                    </td>

                    <td>

                        {{ $payment->mode_of_payment
                            ?? $payment->payment_method
                            ?? 'N/A'
                        }}

                    </td>

                    <td>

                        {{ $payment->reference_no ?? 'N/A' }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="4" class="no-payment">
                        No payment history.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    {{-- =========================================================
         SIGNATURES
    ========================================================== --}}

    <table class="signature-table">

        <tr>

            <td>

                <div class="signature-line">
                    Prepared By
                </div>

            </td>

            <td>

                <div class="signature-line">
                    Checked By
                </div>

            </td>

            <td>

                <div class="signature-line">
                    Approved By
                </div>

            </td>

        </tr>

    </table>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <div class="footer">

        <span class="footer-brand">
            JJE ENTERPRISES
        </span>

        <br>

        This document is computer generated.

    </div>

</div>

<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>

</body>
</html>