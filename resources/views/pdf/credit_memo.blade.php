<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Credit Memo - {{ $creditMemo->credit_memo_no }}</title>

    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 18mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
        }

        /* =========================
           HEADER
        ========================= */

        .header {
            width: 100%;
            border-bottom: 2px solid #381757;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .company {
            width: 60%;
            float: left;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #381757;
            margin-bottom: 4px;
        }

        .company-info {
            font-size: 9px;
            line-height: 1.5;
            color: #555;
        }

        .document-title {
            width: 40%;
            float: right;
            text-align: right;
        }

        .document-title h1 {
            margin: 0;
            font-size: 22px;
            color: #381757;
            letter-spacing: 1px;
        }

        .document-title .status {
            margin-top: 5px;
            display: inline-block;
            padding: 4px 12px;
            border: 1px solid #381757;
            color: #381757;
            font-weight: bold;
            font-size: 9px;
        }

        .clearfix {
            clear: both;
        }

        /* =========================
           DOCUMENT INFORMATION
        ========================= */

        .info-wrapper {
            width: 100%;
            margin-bottom: 18px;
        }

        .info-left {
            width: 55%;
            float: left;
        }

        .info-right {
            width: 45%;
            float: right;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-label {
            width: 125px;
            font-weight: bold;
            color: #555;
        }

        .info-value {
            color: #222;
        }

        /* =========================
           RETURN TYPE
        ========================= */

        .return-type {
            margin-bottom: 15px;
            padding: 8px 10px;
            border-left: 4px solid #381757;
            background: #f5f2f8;
        }

        .return-type-label {
            font-weight: bold;
            color: #381757;
        }

        /* =========================
           DETAILS TABLE
        ========================= */

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .details-table thead th {
            background: #381757;
            color: white;
            font-size: 9px;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #381757;
        }

        .details-table tbody td {
            padding: 7px 6px;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        .details-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .description {
            font-weight: 500;
        }

        /* =========================
           TOTALS
        ========================= */

        .totals-wrapper {
            width: 100%;
            margin-top: 15px;
        }

        .totals {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }

        .totals .label {
            font-weight: bold;
            text-align: left;
        }

        .totals .amount {
            text-align: right;
        }

        .totals .grand-total td {
            font-size: 13px;
            font-weight: bold;
            color: #381757;
            border-top: 2px solid #381757;
            border-bottom: 2px solid #381757;
            padding-top: 9px;
            padding-bottom: 9px;
        }

        /* =========================
           NOTES
        ========================= */

        .remarks {
            margin-top: 25px;
        }

        .remarks-title {
            font-weight: bold;
            color: #381757;
            margin-bottom: 5px;
        }

        .remarks-box {
            border: 1px solid #ddd;
            min-height: 55px;
            padding: 8px;
        }

        /* =========================
           SIGNATURES
        ========================= */

        .signatures {
            width: 100%;
            margin-top: 55px;
        }

        .signature {
            width: 30%;
            display: inline-block;
            vertical-align: top;
            margin-right: 3%;
            text-align: center;
        }

        .signature:last-child {
            margin-right: 0;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 35px;
            padding-top: 5px;
        }

        .signature-name {
            font-weight: bold;
        }

        .signature-label {
            color: #666;
            font-size: 9px;
        }

        /* =========================
           FOOTER
        ========================= */

        .footer {
            position: fixed;
            bottom: -8mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #777;
        }

        .footer-line {
            border-top: 1px solid #ddd;
            margin-bottom: 4px;
        }

        .page-number:after {
            content: counter(page);
        }

        /* =========================
           PRINT
        ========================= */

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

@php
    /*
    |--------------------------------------------------------------------------
    | Customer Name
    |--------------------------------------------------------------------------
    |
    | Format:
    | STORE NAME - FIRST NAME LAST NAME
    |
    */

    $customer = $creditMemo->customer;

    $storeName = trim($customer?->store_name ?? '');
    $firstName = trim($customer?->first_name ?? '');
    $lastName  = trim($customer?->last_name ?? '');

    $fullName = trim($firstName . ' ' . $lastName);

    if ($storeName && $fullName) {
        $customerName = $storeName . ' - ' . $fullName;
    } elseif ($storeName) {
        $customerName = $storeName;
    } elseif ($fullName) {
        $customerName = $fullName;
    } else {
        $customerName = 'N/A';
    }
@endphp


<div class="container">

    {{-- =========================
         COMPANY HEADER
    ========================= --}}

    <div class="header">

        <div class="company">

            <div class="company-name">
                JJE ENTERPRISES
            </div>

            <div class="company-info">
                 456 Caggay Maharlika Highway<br>
                 Tuguegarao City, Cagayan - Tel. No. (078) 304-9210
            </div>

        </div>

        <div class="document-title">

            <h1>CREDIT MEMO</h1>

        </div>

        <div class="clearfix"></div>

    </div>


    {{-- =========================
         DOCUMENT INFORMATION
    ========================= --}}

    <div class="info-wrapper">

        <div class="info-left">

            <table class="info-table">

                <tr>
                    <td class="info-label">
                        Credit Memo No.
                    </td>

                    <td class="info-value">
                        {{ $creditMemo->credit_memo_no ?? 'N/A' }}
                    </td>
                </tr>

                <tr>
                    <td class="info-label">
                        Reference No.
                    </td>

                    <td class="info-value">
                        {{ $creditMemo->invoice?->order_no ?? 'N/A' }}
                    </td>
                </tr>

                <tr>
                    <td class="info-label">
                        Credit Memo Date
                    </td>

                    <td class="info-value">
                        {{ $creditMemo->credit_memo_date
                            ? \Carbon\Carbon::parse($creditMemo->credit_memo_date)->format('M d, Y')
                            : 'N/A'
                        }}
                    </td>
                </tr>

                <tr>
                    <td class="info-label">
                        Credit Memo Type
                    </td>

                    <td class="info-value">
                        {{ $creditMemo->credit_memo_type ?? 'N/A' }}
                    </td>
                </tr>

            </table>

        </div>


        <div class="info-right">

            <table class="info-table">

                <tr>
                    <td class="info-label">
                        Customer
                    </td>

                    <td class="info-value">
                        {{ $customerName }}
                    </td>
                </tr>

                <tr>
                    <td class="info-label">
                        Warehouse
                    </td>

                    <td class="info-value">
                        {{ $creditMemo->warehouse?->warehouse_name ?? 'N/A' }}
                    </td>
                </tr>

                <tr>
                    <td class="info-label">
                        Status
                    </td>

                    <td class="info-value">
                        {{ $creditMemo->status ?? 'N/A' }}
                    </td>
                </tr>


            </table>

        </div>

        <div class="clearfix"></div>

    </div>


    {{-- =========================
         RETURN TYPE
    ========================= --}}

    <div class="return-type">

        <span class="return-type-label">
            Return Type:
        </span>

        {{ $creditMemo->credit_memo_type ?? 'N/A' }}

    </div>


    {{-- =========================
         CREDIT MEMO DETAILS
    ========================= --}}

    <table class="details-table">

        <thead>

            <tr>

                <th style="width: 48%;">
                    Description
                </th>

                <th style="width: 12%;" class="text-center">
                    Quantity
                </th>

                <th style="width: 12%;" class="text-center">
                    Unit
                </th>

                <th style="width: 14%;" class="text-right">
                    Price
                </th>

                <th style="width: 14%;" class="text-right">
                    Amount
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($creditMemo->details as $detail)

                <tr>

                    {{-- DESCRIPTION --}}
                    <td class="description">
                        {{ $detail->description
                            ?? $detail->product?->product_description
                            ?? 'N/A'
                        }}
                    </td>

                    {{-- QUANTITY --}}
                    <td class="text-center">
                        {{ number_format(
                            (float) ($detail->quantity ?? 0),
                            2
                        ) }}
                    </td>

                    {{-- UNIT FROM INVENTORY PER WAREHOUSE --}}
                    <td class="text-center">
                        {{ $detail->inventory_unit ?? 'N/A' }}
                    </td>

                    {{-- PRICE --}}
                    <td class="text-right">
                        {{ number_format(
                            (float) ($detail->selling_price ?? 0),
                            2
                        ) }}
                    </td>

                    {{-- AMOUNT --}}
                    <td class="text-right">
                        {{ number_format(
                            (float) ($detail->amount ?? 0),
                            2
                        ) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5" class="text-center">
                        No credit memo details available.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- =========================
         TOTAL
    ========================= --}}

    <div class="totals-wrapper">

        <table class="totals">

            <tr class="grand-total">

                <td class="label">
                    CREDIT MEMO TOTAL
                </td>

                <td class="amount">
                    ₱ {{ number_format(
                        (float) $creditMemo->details->sum('amount'),
                        2
                    ) }}
                </td>

            </tr>

        </table>

    </div>


    {{-- =========================
         REMARKS
    ========================= --}}

    <div class="remarks">

        <div class="remarks-title">
            Remarks
        </div>

        <div class="remarks-box">
            {{ $creditMemo->remarks ?? '' }}
        </div>

    </div>


    {{-- =========================
         SIGNATURES
    ========================= --}}

    <div class="signatures">

        <div class="signature">

            <div class="signature-line">

                <div class="signature-label">
                    Prepared By
                </div>

            </div>

        </div>


        <div class="signature">

            <div class="signature-line">

                <div class="signature-label">
                    Checked By
                </div>

            </div>

        </div>


        <div class="signature">

            <div class="signature-line">

                <div class="signature-label">
                    Approved By
                </div>

            </div>

        </div>

    </div>

</div>

<!-- {{-- =========================
     FOOTER
========================= --}}

<div class="footer">

    <div class="footer-line"></div>

    Credit Memo No:
    {{ $creditMemo->credit_memo_no ?? 'N/A' }}

    &nbsp; | &nbsp;

    Printed:
    {{ now()->format('M d, Y h:i A') }}

    &nbsp; | &nbsp;

    Page <span class="page-number"></span>

</div> -->

<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>

</body>
</html>