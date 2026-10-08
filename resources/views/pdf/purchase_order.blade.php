<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <style>

        @page {
            size: A4 portrait;
            margin: 10mm 18mm 12mm 18mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #1f2937;
            background: #fff;
        }

        .po-container {
            width: 100%;
            max-width: 100%;
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .header {
            width: 100%;
            border-bottom: 2px solid #381757;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .company-section {
            width: 58%;
            vertical-align: top;
        }

        .document-section {
            width: 42%;
            text-align: right;
            vertical-align: top;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #381757;
            margin: 0 0 3px 0;
        }

        .company-subtitle {
            font-size: 8px;
            color: #555;
            line-height: 1.5;
        }

        .document-title {
            font-size: 22px;
            font-weight: bold;
            color: #381757;
            letter-spacing: 1px;
            margin: 0;
        }

        .status-badge {
            display: inline-block;
            margin-top: 7px;
            padding: 4px 12px;
            background: #381757;
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 3px;
        }

        /* =========================================================
           SECTION TITLE
        ========================================================= */

        .section-title {
            margin-top: 5px;
            margin-bottom: 6px;
            font-size: 9px;
            font-weight: bold;
            color: #381757;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* =========================================================
           PURCHASE ORDER INFORMATION
        ========================================================= */

        .info-wrapper {
            width: 100%;
            margin-bottom: 14px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d9d9df;
        }

        .info-table td {
            padding: 7px 9px;
            border-right: 1px solid #d9d9df;
            border-bottom: 1px solid #d9d9df;
            vertical-align: top;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .info-table td:last-child {
            border-right: none;
        }

        .info-label {
            display: block;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #7b7b84;
            margin-bottom: 3px;
        }

        .info-value {
            display: block;
            font-size: 9px;
            font-weight: bold;
            color: #1f2937;
        }

        .supplier-value {
            font-size: 10px;
            color: #381757;
        }

        /* =========================================================
           ITEMS TABLE
        ========================================================= */

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        .items-table thead {
            display: table-header-group;
        }

        .items-table tfoot {
            display: table-footer-group;
        }

        .items-table tr {
            page-break-inside: avoid;
        }

        .items-table th {
            background: #381757;
            color: #fff;
            border: 1px solid #381757;
            padding: 7px 5px;
            font-size: 7.5px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .2px;
        }

        .items-table td {
            border: 1px solid #d9d9df;
            padding: 6px 5px;
            font-size: 8px;
            vertical-align: middle;
        }

        .items-table tbody tr:nth-child(even) td {
            background: #fafafa;
        }

        .product {
            text-align: left;
            font-weight: 600;
            color: #222;
        }

        .center {
            text-align: center;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }

        .currency {
            text-align: right;
            white-space: nowrap;
        }

        /* =========================================================
           TOTAL ROW
        ========================================================= */

        .items-table tfoot tr {
            page-break-inside: avoid;
        }

        .items-total-row td {
            background: #f3eef7;
            border: 1px solid #381757;
            border-top: 2px solid #381757;
            padding: 7px 5px;
            font-size: 8px;
            font-weight: bold;
            color: #381757;
        }

        .items-total-row td:last-child {
            font-size: 8.5px;
        }

        /* =========================================================
           TOTALS AREA
        ========================================================= */

        .totals-area {
            width: 100%;
            margin-top: 12px;
        }

        .totals-layout {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-right {
            width: 100%;
            vertical-align: top;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 7px;
            border-bottom: 1px solid #e2e2e2;
            font-size: 8.5px;
        }

        .summary-label {
            text-align: left;
            color: #555;
            font-weight: bold;
        }

        .summary-amount {
            text-align: right;
            font-weight: bold;
            white-space: nowrap;
        }

        .summary-total td {
            background: #381757;
            color: #fff;
            border-bottom: none;
            padding: 8px 7px;
            font-size: 11px;
            font-weight: bold;
        }

        .summary-total .summary-label {
            color: #fff;
        }

        .summary-total .summary-amount {
            color: #fff;
        }

        /* =========================================================
           WEIGHT
        ========================================================= */

        .weight-box {
            margin-top: 6px;
            border: 1px solid #d9d9df;
            background: #f7f7f9;
        }

        .weight-table {
            width: 100%;
            border-collapse: collapse;
        }

        .weight-table td {
            padding: 6px 7px;
            font-size: 8px;
        }

        .weight-label {
            font-weight: bold;
            color: #555;
        }

        .weight-value {
            text-align: right;
            font-weight: bold;
            color: #381757;
        }

        /* =========================================================
           APPROVAL
        ========================================================= */

        .approval-title {
            margin-top: 28px;
            margin-bottom: 7px;
            font-size: 8px;
            font-weight: bold;
            color: #381757;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .signature-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-left: -10px;
        }

        .signature-table td {
            width: 33.33%;
            padding: 0 10px;
            vertical-align: bottom;
        }

        .signature-line {
            height: 32px;
            border-bottom: 1px solid #555;
        }

        .signature-label {
            text-align: center;
            margin-top: 5px;
            font-size: 7.5px;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
        }

        .signature-sub {
            text-align: center;
            margin-top: 2px;
            font-size: 7px;
            color: #999;
        }

        /* =========================================================
           FOOTER
        ========================================================= */

        .footer {
            margin-top: 18px;
            padding-top: 7px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 7px;
            color: #888;
            line-height: 1.4;
        }

        .footer strong {
            color: #381757;
        }

        /* =========================================================
           PRINT
        ========================================================= */

        @media print {

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .items-table th {
                background-color: #381757 !important;
                color: #fff !important;
            }

            .items-total-row td {
                background-color: #f3eef7 !important;
                color: #381757 !important;
            }

            .summary-total td {
                background-color: #381757 !important;
                color: #fff !important;
            }

            .status-badge {
                background-color: #381757 !important;
                color: #fff !important;
            }
        }

    </style>

</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | Purchase Order Totals
    |--------------------------------------------------------------------------
    */

    $totalLines = $items->count();

    $totalQuantity = $items->sum(
        fn ($item) => (float) $item->quantity
    );

    $totalCost = $items->sum(
        fn ($item) => (float) $item->total_cost
    );

    $discountAmount = $items->sum(
        fn ($item) => (float) $item->discount_amount
    );

    $netAmount = $items->sum(
        fn ($item) => (float) $item->net_amount
    );

    $totalWeight = $items->sum(
        fn ($item) => (float) ($item->tag_weight ?? 0)
    );

@endphp


<div class="po-container">


    <!-- =========================================================
         HEADER
    ========================================================= -->

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="company-section">

                    <div class="company-name">
                        JJE ENTERPRISES
                    </div>

                    <div class="company-subtitle">
                        456 Caggay Maharlika Highway<br>
                        Tuguegarao City, Cagayan - Tel. No. (078) 304-9210
                    </div>

                </td>

                <td class="document-section">

                    <div class="document-title">
                        PURCHASE ORDER
                    </div>


                </td>

            </tr>

        </table>

    </div>


    <!-- =========================================================
         PURCHASE ORDER INFORMATION
    ========================================================= -->

    <div class="section-title">
        Purchase Order Information
    </div>

    <div class="info-wrapper">

        <table class="info-table">

            <tr>

                <td style="width: 35%;">

                    <span class="info-label">
                        Supplier
                    </span>

                    <span class="info-value supplier-value">
                        {{ $record->supplier->company_name ?? '—' }}
                    </span>

                </td>

                <td style="width: 21%;">

                    <span class="info-label">
                        Purchase Order No.
                    </span>

                    <span class="info-value">
                        {{ $record->purchase_order_no ?? '—' }}
                    </span>

                </td>

                <td style="width: 22%;">

                    <span class="info-label">
                        Warehouse
                    </span>

                    <span class="info-value">
                        {{ $record->warehouse->warehouse_name ?? '—' }}
                    </span>

                </td>

                <td style="width: 22%;">

                    <span class="info-label">
                        Payment Terms
                    </span>

                    <span class="info-value">
                        {{ $record->paymentTerms->payment_terms ?? '—' }}
                    </span>

                </td>

            </tr>

            <tr>

                <td>

                    <span class="info-label">
                        Supplier Invoice No.
                    </span>

                    <span class="info-value">
                        {{ $record->invoice_no ?: '—' }}
                    </span>

                </td>

                <td>

                    <span class="info-label">
                        Received Date
                    </span>

                    <span class="info-value">
                        {{ $record->received_date ?: '—' }}
                    </span>

                </td>

                <td>

                    <span class="info-label">
                        Order Date
                    </span>

                    <span class="info-value">

                        {{ $record->created_at
                            ? \Carbon\Carbon::parse($record->created_at)->format('M d, Y')
                            : '—'
                        }}

                    </span>

                </td>

                <td>

                    <span class="info-label">
                        Status
                    </span>

                    <span class="info-value">
                        {{ $record->status ?? '—' }}
                    </span>

                </td>

            </tr>

        </table>

    </div>


    <!-- =========================================================
         ORDER DETAILS
    ========================================================= -->

    <div class="section-title">
        Order Details
    </div>

    <table class="items-table">

        <thead>

            <tr>

                <th style="width: 5%;">
                    LINE
                </th>

                <th style="width: 25%;">
                    Product Description
                </th>

                <th style="width: 7%;">
                    UOM
                </th>

                <th style="width: 8%;">
                    Qty
                </th>

                <th style="width: 9%;">
                    Weight
                </th>

                <th style="width: 11%;">
                    Unit Cost
                </th>

                <th style="width: 12%;">
                    Total Cost
                </th>

                <th style="width: 10%;">
                    Discount
                </th>

                <th style="width: 13%;">
                    Net Amount
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse ($items as $index => $item)

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td class="product">
                        {{ $products[$index]->product_description ?? '—' }}
                    </td>

                    <td class="center">
                        {{ $uom[$index]->unit_code ?? '—' }}
                    </td>

                    <td class="number">
                        {{ number_format((float) $item->quantity, 2) }}
                    </td>

                    <td class="number">
                        {{ number_format((float) ($item->tag_weight ?? 0), 2) }} kg
                    </td>

                    <td class="currency">
                        ₱{{ number_format((float) $item->unit_cost, 2) }}
                    </td>

                    <td class="currency">
                        ₱{{ number_format((float) $item->total_cost, 2) }}
                    </td>

                    <td class="number">
                        {{ $item->discount_rate ?: '—' }}
                    </td>

                    <td class="currency">
                        ₱{{ number_format((float) $item->net_amount, 2) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="9" class="center">
                        No items found.
                    </td>

                </tr>

            @endforelse

        </tbody>


        <!-- =====================================================
             TOTAL LINE
        ===================================================== -->

        <tfoot>

            <tr class="items-total-row">

                <td colspan="3" style="text-align: right;">

                    TOTAL —
                    {{ $totalLines }}
                    {{ $totalLines == 1 ? 'LINE' : 'LINES' }}

                </td>

                <td class="number">
                    {{ number_format($totalQuantity, 2) }}
                </td>

                <td class="number">
                    {{ number_format($totalWeight, 2) }} kg
                </td>

                <td class="number">
                    —
                </td>

                <td class="currency">
                    ₱{{ number_format($totalCost, 2) }}
                </td>

                <td class="currency">
                    ₱{{ number_format($discountAmount, 2) }}
                </td>

                <td class="currency">
                    ₱{{ number_format($netAmount, 2) }}
                </td>

            </tr>

        </tfoot>

    </table>


    <!-- =========================================================
         TOTALS
    ========================================================= -->

    <div class="totals-area">

        <table class="totals-layout">

            <tr>

                <td class="totals-right">

                    <table class="summary-table">

                        <tr>

                            <td class="summary-label">
                                Total Cost
                            </td>

                            <td class="summary-amount">
                                ₱{{ number_format($totalCost, 2) }}
                            </td>

                        </tr>

                        <tr>

                            <td class="summary-label">
                                Total Discount
                            </td>

                            <td class="summary-amount">
                                ₱{{ number_format($discountAmount, 2) }}
                            </td>

                        </tr>

                        <tr class="summary-total">

                            <td class="summary-label">
                                NET AMOUNT
                            </td>

                            <td class="summary-amount">
                                ₱{{ number_format($netAmount, 2) }}
                            </td>

                        </tr>

                    </table>


                    <!-- TOTAL WEIGHT -->

                    <div class="weight-box">

                        <table class="weight-table">

                            <tr>

                                <td class="weight-label">
                                    Total Weight
                                </td>

                                <td class="weight-value">
                                    {{ number_format($totalWeight, 2) }} kg
                                </td>

                            </tr>

                        </table>

                    </div>

                </td>

            </tr>

        </table>

    </div>


    <!-- =========================================================
         APPROVAL
    ========================================================= -->

    <div class="approval-title">
        Approval & Authorization
    </div>

    <table class="signature-table">

        <tr>

            <td>

                <div class="signature-line"></div>

                <div class="signature-label">
                    Prepared By
                </div>

                <div class="signature-sub">
                    Signature / Date
                </div>

            </td>

            <td>

                <div class="signature-line"></div>

                <div class="signature-label">
                    Checked By
                </div>

                <div class="signature-sub">
                    Signature / Date
                </div>

            </td>

            <td>

                <div class="signature-line"></div>

                <div class="signature-label">
                    Approved By
                </div>

                <div class="signature-sub">
                    Signature / Date
                </div>

            </td>

        </tr>

    </table>


    <!-- =========================================================
         FOOTER
    ========================================================= -->

    <div class="footer">

        <strong>JJE Enterprises</strong>
        &nbsp;•&nbsp;
        Purchase Order generated by the ERP system.
        <br>

        This document is valid only when properly authorized and approved.

    </div>


</div>

</body>

</html>