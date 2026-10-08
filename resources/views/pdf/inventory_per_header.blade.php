<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4;
            margin: 12mm 14mm 15mm 14mm;
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
            background: #fff;
        }

        .report {
            width: 100%;
        }

        /* =====================================================
           HEADER
        ===================================================== */

        .header {
            width: 100%;
            border-bottom: 2px solid #381757;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .company-name {
            font-size: 21px;
            font-weight: bold;
            color: #381757;
            margin-bottom: 3px;
        }

        .company-address {
            font-size: 8px;
            color: #666;
            line-height: 1.5;
        }

        .document-box {
            text-align: right;
        }

        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #381757;
            letter-spacing: 1px;
        }

        .document-subtitle {
            font-size: 8px;
            color: #777;
            margin-top: 3px;
        }

        /* =====================================================
           DOCUMENT INFORMATION
        ===================================================== */

        .info-section {
            margin-bottom: 12px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px 7px;
            vertical-align: top;
        }

        .info-label {
            display: block;
            font-size: 7px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 2px;
            font-weight: bold;
        }

        .info-value {
            font-size: 9px;
            font-weight: bold;
            color: #222;
        }

        .status {
            display: inline-block;
            padding: 3px 9px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 3px;
            background: #eee;
        }

        .status-posted {
            background: #e7f6ec;
            color: #18743a;
        }

        .status-draft {
            background: #fff4db;
            color: #946200;
        }

        /* =====================================================
           TRANSFER INFORMATION
        ===================================================== */

        .transfer-section {
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .transfer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transfer-box {
            border: 1px solid #d5d5d5;
            padding: 9px 10px;
            height: 58px;
            vertical-align: middle;
        }

        .transfer-box-title {
            font-size: 7px;
            text-transform: uppercase;
            color: #777;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .transfer-name {
            font-size: 10px;
            font-weight: bold;
            color: #381757;
        }

        .arrow {
            text-align: center;
            vertical-align: middle;
            width: 8%;
            font-size: 18px;
            color: #381757;
            font-weight: bold;
        }

        /* =====================================================
           SECTION TITLE
        ===================================================== */

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #381757;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid #381757;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        /* =====================================================
           ITEMS TABLE
        ===================================================== */

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .items-table thead {
            display: table-header-group;
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
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .items-table td {
            border: 1px solid #d8d8d8;
            padding: 6px 5px;
            font-size: 8px;
            vertical-align: middle;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .product-code {
            text-align: center;
            font-weight: bold;
            white-space: nowrap;
        }

        .description {
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }

        .remarks {
            text-align: left;
            color: #555;
        }

        /* =====================================================
           TOTALS
        ===================================================== */

        .summary-wrapper {
            width: 100%;
            margin-top: 12px;
        }

        .summary-table {
            width: 42%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
        }

        .summary-label {
            text-align: left;
            font-size: 8px;
            color: #555;
            font-weight: bold;
        }

        .summary-value {
            text-align: right;
            font-size: 9px;
            font-weight: bold;
        }

        .grand-total td {
            background: #f3eff7;
            border-top: 2px solid #381757;
            border-bottom: 2px solid #381757;
            padding: 7px 8px;
        }

        .grand-total .summary-label {
            color: #381757;
            font-size: 9px;
        }

        .grand-total .summary-value {
            color: #381757;
            font-size: 12px;
        }

        /* =====================================================
           ASSIGNMENT
        ===================================================== */

        .assignment-section {
            margin-top: 18px;
        }

        .assignment-table {
            width: 100%;
            border-collapse: collapse;
        }

        .assignment-table td {
            width: 50%;
            border: 1px solid #ddd;
            padding: 8px;
        }

        .assignment-label {
            font-size: 7px;
            text-transform: uppercase;
            color: #777;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .assignment-value {
            font-size: 9px;
            font-weight: bold;
        }

        /* =====================================================
           SIGNATURES
        ===================================================== */

        .signature-section {
            margin-top: 45px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 33.33%;
            padding: 0 12px;
            vertical-align: bottom;
        }

        .signature-line {
            height: 28px;
            border-bottom: 1px solid #222;
        }

        .signature-label {
            text-align: center;
            margin-top: 5px;
            font-size: 8px;
            font-weight: bold;
            color: #444;
        }

        .signature-sub {
            text-align: center;
            font-size: 7px;
            color: #888;
            margin-top: 2px;
        }

        /* =====================================================
           FOOTER
        ===================================================== */

        .footer {
            margin-top: 22px;
            padding-top: 7px;
            border-top: 1px solid #ddd;
            font-size: 7px;
            color: #888;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            text-align: left;
        }

        .footer-right {
            text-align: right;
        }

        /* =====================================================
           PRINT
        ===================================================== */

        @media print {

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .items-table th {
                background: #381757 !important;
                color: #fff !important;
            }

            .grand-total td {
                background: #f3eff7 !important;
            }

        }
    </style>
</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | TOTALS
    |--------------------------------------------------------------------------
    */

    $totalLines = $items->count();

    $totalPacks = $items->sum(
        fn ($item) => (float) ($item->quantity ?? 0)
    );

    $totalWeight = $items->sum(
        fn ($item) => (float) ($item->weight ?? 0)
    );

    $totalCost = $items->sum(
        fn ($item) => (float) ($item->gross_amount ?? 0)
    );

@endphp


<div class="report">

    <!-- =====================================================
         COMPANY HEADER
    ====================================================== -->

    <div class="header">

        <table class="header-table">

            <tr>

                <td style="width: 60%;">

                    <div class="company-name">
                        JJE Enterprises
                    </div>

                    <div class="company-address">
                        456 Caggay Maharlika Highway<br>
                        Tuguegarao City, Cagayan
                    </div>

                </td>

                <td style="width: 40%;" class="document-box">

                    <div class="document-title">
                        INVENTORY
                    </div>

                    <div class="document-subtitle">
                        INVENTORY TRANSFER / MOVEMENT REPORT
                    </div>

                </td>

            </tr>

        </table>

    </div>


    <!-- =====================================================
         DOCUMENT INFORMATION
    ====================================================== -->

    <div class="info-section">

        <table class="info-table">

            <tr>

                <td style="width: 20%;">

                    <span class="info-label">
                        Document No.
                    </span>

                    <span class="info-value">
                        {{ $record->document_no ?: '—' }}
                    </span>

                </td>

                <td style="width: 20%;">

                    <span class="info-label">
                        Transfer Date
                    </span>

                    <span class="info-value">
                        {{ $record->transfer_date ?: '—' }}
                    </span>

                </td>

                <td style="width: 20%;">

                    <span class="info-label">
                        Inventory Type
                    </span>

                    <span class="info-value">
                        {{ $record->inventory_type?->inventory_type ?: '—' }}
                    </span>

                </td>

                <td style="width: 20%;">

                    <span class="info-label">
                        Plate No.
                    </span>

                    <span class="info-value">
                        {{ $record->plate_no ?: '—' }}
                    </span>

                </td>

                <td style="width: 20%;">

                    <span class="info-label">
                        Status
                    </span>

                    @if (($record->status ?? '') === 'Posted')

                        <span class="status status-posted">
                            Posted
                        </span>

                    @else

                        <span class="status status-draft">
                            {{ $record->status ?: 'Draft' }}
                        </span>

                    @endif

                </td>

            </tr>

        </table>

    </div>


    <!-- =====================================================
         TRANSFER INFORMATION
    ====================================================== -->

    <div class="section-title">
        Transfer Information
    </div>

    <div class="transfer-section">

        <table class="transfer-table">

            <tr>

                <td class="transfer-box" style="width: 46%;">

                    <div class="transfer-box-title">
                        Transfer From
                    </div>

                    <div class="transfer-name">

                        {{ $record->warehouseFrom?->warehouse_name
                            ?: '—' }}

                    </div>

                </td>

                <td class="arrow">
                    →
                </td>

                <td class="transfer-box" style="width: 46%;">

                    <div class="transfer-box-title">
                        Transfer To
                    </div>

                    <div class="transfer-name">

                        {{ $record->warehouseTo?->warehouse_name
                            ?: '—' }}

                    </div>

                </td>

            </tr>

        </table>

    </div>


    <!-- =====================================================
         ITEMS
    ====================================================== -->

    <div class="section-title">
        Inventory Details
    </div>

    <table class="items-table">

        <thead>

            <tr>

                <th style="width: 10%;">
                    Product Code
                </th>

                <th style="width: 24%;">
                    Description
                </th>

                <th style="width: 7%;">
                    Unit
                </th>

                <th style="width: 9%;">
                    Weight
                </th>

                <th style="width: 9%;">
                    Packs
                </th>

                <th style="width: 12%;">
                    Unit Cost
                </th>

                <th style="width: 14%;">
                    Total Cost
                </th>

                <th style="width: 15%;">
                    Remarks
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse ($items as $index => $item)

                <tr>

                    <td class="product-code">
                        {{ $products[$index]->product_code ?? '—' }}
                    </td>

                    <td class="description">
                        {{ $products[$index]->product_description ?? '—' }}
                    </td>

                    <td class="center">
                        {{ $uom[$index]->unit_code ?? '—' }}
                    </td>

                    <td class="number">
                        {{ number_format((float) ($item->weight ?? 0), 2) }}
                        kg
                    </td>

                    <td class="number">
                        {{ number_format((float) ($item->quantity ?? 0), 2) }}
                    </td>

                    <td class="number">
                        ₱{{ number_format((float) ($item->unit_cost ?? 0), 2) }}
                    </td>

                    <td class="number">
                        ₱{{ number_format((float) ($item->gross_amount ?? 0), 2) }}
                    </td>

                    <td class="remarks">
                        {{ $item->remarks ?: '—' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="8"
                        style="text-align:center; padding:15px; color:#888;">

                        No inventory items found.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <!-- =====================================================
         SUMMARY
    ====================================================== -->

    <div class="summary-wrapper">

        <table class="summary-table">

            <tr>

                <td class="summary-label">
                    Total Lines
                </td>

                <td class="summary-value">
                    {{ number_format($totalLines) }}
                </td>

            </tr>

            <tr>

                <td class="summary-label">
                    Total Packs
                </td>

                <td class="summary-value">
                    {{ number_format($totalPacks, 2) }}
                </td>

            </tr>

            <tr>

                <td class="summary-label">
                    Total Weight
                </td>

                <td class="summary-value">
                    {{ number_format($totalWeight, 2) }} kg
                </td>

            </tr>

            <tr class="grand-total">

                <td class="summary-label">
                    TOTAL COST
                </td>

                <td class="summary-value">
                    ₱{{ number_format($totalCost, 2) }}
                </td>

            </tr>

        </table>

    </div>


    <!-- =====================================================
         ASSIGNMENT
    ====================================================== -->

    <div class="assignment-section">

        <div class="section-title">
            Assignment
        </div>

        <table class="assignment-table">

            <tr>

                <td>

                    <div class="assignment-label">
                        Assigned From
                    </div>

                    <div class="assignment-value">
                        {{ $record->assigned_from ?: '—' }}
                    </div>

                </td>

                <td>

                    <div class="assignment-label">
                        Assigned To
                    </div>

                    <div class="assignment-value">
                        {{ $record->assigned_to ?: '—' }}
                    </div>

                </td>

            </tr>

        </table>

    </div>


    <!-- =====================================================
         SIGNATURES
    ====================================================== -->

    <div class="signature-section">

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
                        Released By
                    </div>

                    <div class="signature-sub">
                        Signature / Date
                    </div>

                </td>

                <td>

                    <div class="signature-line"></div>

                    <div class="signature-label">
                        Received By
                    </div>

                    <div class="signature-sub">
                        Signature / Date
                    </div>

                </td>

            </tr>

        </table>

    </div>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <div class="footer">

        <table class="footer-table">

            <tr>

                <td class="footer-left">
                    JJE Enterprises &nbsp; | &nbsp; Inventory Management System
                </td>

                <td class="footer-right">
                    Document No.: {{ $record->document_no ?: '—' }}
                </td>

            </tr>

        </table>

        <div style="text-align:center; margin-top:5px;">
            This document is system generated and does not require a signature
            unless otherwise required by company policy.
        </div>

    </div>

</div>

</body>
</html>
