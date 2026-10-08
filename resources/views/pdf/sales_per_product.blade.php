<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Sales Per Product</title>

    <style>

        @page {
            size: A4 landscape;
            margin: 12mm 10mm 15mm 10mm;
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
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
        }

        .header h2 {
            font-size: 12px;
            margin: 0;
            font-weight: normal;
        }

        .date-range {
            text-align: center;
            margin-top: 5px;
            font-size: 10px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 8px;
            padding: 6px;
            border: 1px solid #222;
        }

        .product-title {
            font-size: 11px;
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        th {
            background: #eeeeee;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #777;
            padding: 4px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .product-total {
            font-weight: bold;
            text-align: right;
            border-top: 2px solid #222;
        }

        .section-total {
            margin-top: 10px;
            margin-bottom: 15px;
            width: 300px;
            margin-left: auto;
        }

        .section-total td {
            font-weight: bold;
        }

        .grand-total {
            margin-top: 20px;
            width: 350px;
            margin-left: auto;
        }

        .grand-total td {
            font-size: 11px;
            font-weight: bold;
            padding: 6px;
        }

        .no-records {
            text-align: center;
            padding: 15px;
            border: 1px solid #777;
            margin-bottom: 15px;
        }

    </style>

</head>

<body>

<div class="header">

    <h1>JJE ENTERPRISES</h1>

    <h2>SALES PER PRODUCT</h2>

    <div class="date-range">
        {{ $dateFrom->format('M d, Y') }}
        -
        {{ $dateTo->format('M d, Y') }}
    </div>

</div>


{{-- ========================================================= --}}
{{-- CASH INVOICE --}}
{{-- ========================================================= --}}

@if($cashByProduct->count())

    <div class="section-title">
        CASH INVOICE
    </div>

    @foreach($cashByProduct as $productId => $details)

        @php
            $product = $details->first()->product;
        @endphp

        <div class="product-title">
            Product:
            {{ $product?->product_description ?? 'Unknown Product' }}
        </div>

        <table>

            <thead>

                <tr>

                    <th width="10%">
                        Invoice #
                    </th>

                    <th width="9%">
                        Date
                    </th>

                    <th width="20%">
                        Customer
                    </th>

                    <th width="15%">
                        Sales Representative
                    </th>

                    <th width="10%">
                        Terms
                    </th>

                    <th width="7%">
                        Qty
                    </th>

                    <th width="10%">
                        Unit Price
                    </th>

                    <th width="11%">
                        Amount
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($details as $detail)

                    @php
                        $invoice = $detail->report_invoice;

                        $customerName = trim(
                            ($invoice->customer?->store_name ?? '') .
                            ' ' .
                            ($invoice->customer?->first_name ?? '') .
                            ' ' .
                            ($invoice->customer?->last_name ?? '')
                        );

                        $amount = (float) (
                            $detail->amount
                            ?? (
                                ($detail->quantity ?? 0)
                                * ($detail->price ?? 0)
                            )
                        );
                    @endphp

                    <tr>

                        <td>
                            {{ $invoice->order_no }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('m/d/Y') }}
                        </td>

                        <td>
                            {{ $customerName ?: 'N/A' }}
                        </td>

                        <td>
                            {{ trim(
                                ($invoice->employee?->first_name ?? '') .
                                ' ' .
                                ($invoice->employee?->last_name ?? '')
                            ) }}
                        </td>

                        <td>
                            {{ $invoice->paymentTerms?->payment_terms ?? 'N/A' }}
                        </td>

                        <td class="right">
                            {{ number_format($detail->quantity ?? 0, 2) }}
                        </td>

                        <td class="right">
                            {{ number_format($detail->price ?? 0, 2) }}
                        </td>

                        <td class="right">
                            {{ number_format($amount, 2) }}
                        </td>

                    </tr>

                @endforeach

                <tr>

                    <td colspan="7" class="product-total">
                        PRODUCT TOTAL
                    </td>

                    <td class="right product-total">

                        {{ number_format(
                            $details->sum(function ($detail) {
                                return (float) (
                                    $detail->amount
                                    ?? (
                                        ($detail->quantity ?? 0)
                                        * ($detail->price ?? 0)
                                    )
                                );
                            }),
                            2
                        ) }}

                    </td>

                </tr>

            </tbody>

        </table>

    @endforeach


    <table class="section-total">

        <tr>

            <td>
                CASH SALES
            </td>

            <td class="right">
                {{ number_format($cashTotal, 2) }}
            </td>

        </tr>

    </table>

@else

    <div class="section-title">
        CASH INVOICE
    </div>

    <div class="no-records">
        No cash sales found.
    </div>

@endif


{{-- ========================================================= --}}
{{-- CHARGE INVOICE --}}
{{-- ========================================================= --}}

@if($chargeByProduct->count())

    <div class="section-title">
        CHARGE INVOICE
    </div>

    @foreach($chargeByProduct as $productId => $details)

        @php
            $product = $details->first()->product;
        @endphp

        <div class="product-title">
            Product:
            {{ $product?->product_description ?? 'Unknown Product' }}
        </div>

        <table>

            <thead>

                <tr>

                    <th width="10%">
                        Invoice #
                    </th>

                    <th width="9%">
                        Date
                    </th>

                    <th width="20%">
                        Customer
                    </th>

                    <th width="15%">
                        Sales Representative
                    </th>

                    <th width="10%">
                        Terms
                    </th>

                    <th width="7%">
                        Qty
                    </th>

                    <th width="10%">
                        Unit Price
                    </th>

                    <th width="11%">
                        Amount
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($details as $detail)

                    @php
                        $invoice = $detail->report_invoice;

                        $customerName = trim(
                            ($invoice->customer?->store_name ?? '') .
                            ' ' .
                            ($invoice->customer?->first_name ?? '') .
                            ' ' .
                            ($invoice->customer?->last_name ?? '')
                        );

                        $amount = (float) (
                            $detail->amount
                            ?? (
                                ($detail->quantity ?? 0)
                                * ($detail->price ?? 0)
                            )
                        );
                    @endphp

                    <tr>

                        <td>
                            {{ $invoice->order_no  }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('m/d/Y') }}
                        </td>

                        <td>
                            {{ $customerName ?: 'N/A' }}
                        </td>

                        <td>
                            {{ trim(
                                ($invoice->employee?->first_name ?? '') .
                                ' ' .
                                ($invoice->employee?->last_name ?? '')
                            ) }}
                        </td>

                        <td>
                            {{ $invoice->paymentTerms?->payment_terms ?? 'N/A' }}
                        </td>

                        <td class="right">
                            {{ number_format($detail->quantity ?? 0, 2) }}
                        </td>

                        <td class="right">
                            {{ number_format($detail->price ?? 0, 2) }}
                        </td>

                        <td class="right">
                            {{ number_format($amount, 2) }}
                        </td>

                    </tr>

                @endforeach

                <tr>

                    <td colspan="7" class="product-total">
                        PRODUCT TOTAL
                    </td>

                    <td class="right product-total">

                        {{ number_format(
                            $details->sum(function ($detail) {
                                return (float) (
                                    $detail->amount
                                    ?? (
                                        ($detail->quantity ?? 0)
                                        * ($detail->price ?? 0)
                                    )
                                );
                            }),
                            2
                        ) }}

                    </td>

                </tr>

            </tbody>

        </table>

    @endforeach


    <table class="section-total">

        <tr>

            <td>
                CHARGE SALES
            </td>

            <td class="right">
                {{ number_format($chargeTotal, 2) }}
            </td>

        </tr>

    </table>

@else

    <div class="section-title">
        CHARGE INVOICE
    </div>

    <div class="no-records">
        No charge sales found.
    </div>

@endif


{{-- ========================================================= --}}
{{-- GRAND TOTAL --}}
{{-- ========================================================= --}}

<table class="grand-total">

    <tr>

        <td>
            CASH SALES
        </td>

        <td class="right">
            {{ number_format($cashTotal, 2) }}
        </td>

    </tr>

    <tr>

        <td>
            CHARGE SALES
        </td>

        <td class="right">
            {{ number_format($chargeTotal, 2) }}
        </td>

    </tr>

    <tr>

        <td>
            NET SALES
        </td>

        <td class="right">
            {{ number_format($netSale, 2) }}
        </td>

    </tr>

</table>

 <script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
</body>
</html>