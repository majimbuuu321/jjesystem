
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <title>Warehouse Inventory Summary</title>

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
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 16px;
        }

        .details {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .details td {
            padding: 5px;
            border: 1px solid #000;
        }

        .details td:first-child {
            font-weight: bold;
            background: #f2f2f2;
        }

        /*
        |--------------------------------------------------------------------------
        | WAREHOUSE TITLE
        |--------------------------------------------------------------------------
        */

        .warehouse-title {
            font-weight: bold;
            font-size: 16px;
            background: #381757;
            color: #ffffff;
            padding: 8px;
            margin-top: 20px;
            border: 1px solid #000;
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY TITLE
        |--------------------------------------------------------------------------
        */

        .category-title {
            font-weight: bold;
            font-size: 13px;
            background: #e5e5e5;
            padding: 7px;
            margin-top: 10px;
            border: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            text-align: center;
            font-weight: bold;
            background: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY TOTAL
        |--------------------------------------------------------------------------
        */

        .category-total {
            font-weight: bold;
            background: #f2f2f2;
        }

        /*
        |--------------------------------------------------------------------------
        | WAREHOUSE TOTAL
        |--------------------------------------------------------------------------
        */

        .warehouse-total {
            font-weight: bold;
            background: #dddddd;
        }

        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */

        .grand-total {
            background-color: #398757 !important;
            color: #ffffff !important;
            font-weight: bold;

            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .grand-total td {
            padding: 7px 6px;
        }

        /*
        |--------------------------------------------------------------------------
        | NO RECORDS
        |--------------------------------------------------------------------------
        */

        .no-records {
            text-align: center;
            padding: 20px;
            font-weight: bold;
        }

        @media print {

            body {
                margin: 15px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>


<body>

    {{-- HEADER --}}
    <div class="header">

        <h2>
            JJE Enterprises
        </h2>

        <h3>
            Warehouse Inventory Summary
        </h3>

    </div>


    {{-- FILTER DETAILS --}}
    <table class="details">

        {{-- DATE FROM --}}
        <tr>

            <td>
                Date From
            </td>

            <td>
                {{ $dateFrom
                    ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y')
                    : 'N/A'
                }}
            </td>

        </tr>


        {{-- DATE TO --}}
        <tr>

            <td>
                Date To
            </td>

            <td>
                {{ $dateTo
                    ? \Carbon\Carbon::parse($dateTo)->format('M d, Y')
                    : 'N/A'
                }}
            </td>

        </tr>

    </table>


    {{-- GRAND TOTAL VARIABLES --}}
    @php

        $grandQty = 0;
        $grandWeight = 0;
        $grandAmount = 0;

    @endphp


    {{-- NO RECORDS --}}
    @if($groupedRecords->isEmpty())

        <table>

            <tbody>

                <tr>

                    <td class="no-records">
                        No inventory records found.
                    </td>

                </tr>

            </tbody>

        </table>

    @else


        {{-- ========================================================= --}}
        {{-- WAREHOUSES --}}
        {{-- ========================================================= --}}

        @foreach($groupedRecords as $warehouseName => $categories)

            {{-- WAREHOUSE TITLE --}}
            <div class="warehouse-title">

                Warehouse:
                {{ $warehouseName }}

            </div>


            @php

                $warehouseQty = 0;
                $warehouseWeight = 0;
                $warehouseAmount = 0;

            @endphp


            {{-- ===================================================== --}}
            {{-- CATEGORIES --}}
            {{-- ===================================================== --}}

            @foreach($categories as $category => $items)

                <div class="category-title">

                    Category:
                    {{ $category }}

                </div>


                @php

                    $categoryQty = 0;
                    $categoryWeight = 0;
                    $categoryAmount = 0;

                @endphp


                <table>

                    <thead>

                        <tr>

                            <th>
                                Product
                            </th>

                            <th width="120">
                                Quantity
                            </th>

                            <th width="120">
                                Weight
                            </th>

                            <th width="150">
                                Amount
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($items as $item)

                            @php

                                $qty = (float) ($item->quantity ?? 0);

                                $weight =
                                    $qty *
                                    (float) ($item->product?->weight ?? 0);

                                $amount =
                                    $qty *
                                    (float) ($item->product?->unit_cost ?? 0);


                                // Category totals
                                $categoryQty += $qty;
                                $categoryWeight += $weight;
                                $categoryAmount += $amount;


                                // Warehouse totals
                                $warehouseQty += $qty;
                                $warehouseWeight += $weight;
                                $warehouseAmount += $amount;


                                // Grand totals
                                $grandQty += $qty;
                                $grandWeight += $weight;
                                $grandAmount += $amount;

                            @endphp


                            <tr>

                                {{-- PRODUCT --}}
                                <td>

                                    {{ $item->product?->product_description ?? 'N/A' }}

                                </td>


                                {{-- QUANTITY --}}
                                <td class="text-right">

                                    {{ number_format($qty, 2) }}

                                </td>


                                {{-- WEIGHT --}}
                                <td class="text-right">

                                    {{ number_format($weight, 2) }}

                                </td>


                                {{-- AMOUNT --}}
                                <td class="text-right">

                                    {{ number_format($amount, 2) }}

                                </td>

                            </tr>

                        @endforeach


                        {{-- CATEGORY TOTAL --}}
                        <tr class="category-total">

                            <td>
                                Category Total
                            </td>

                            <td class="text-right">
                                {{ number_format($categoryQty, 2) }}
                            </td>

                            <td class="text-right">
                                {{ number_format($categoryWeight, 2) }}
                            </td>

                            <td class="text-right">
                                {{ number_format($categoryAmount, 2) }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            @endforeach


            {{-- ===================================================== --}}
            {{-- WAREHOUSE TOTAL --}}
            {{-- ===================================================== --}}

            <table style="margin-top: 8px;">

                <tbody>

                    <tr class="warehouse-total">

                        <td>
                            {{ $warehouseName }} Total
                        </td>

                        <td width="120" class="text-right">

                            {{ number_format($warehouseQty, 2) }}

                        </td>

                        <td width="120" class="text-right">

                            {{ number_format($warehouseWeight, 2) }}

                        </td>

                        <td width="150" class="text-right">

                            {{ number_format($warehouseAmount, 2) }}

                        </td>

                    </tr>

                </tbody>

            </table>


        @endforeach


        <br>


        {{-- ========================================================= --}}
        {{-- GRAND TOTAL --}}
        {{-- ========================================================= --}}

        <table>

            <tbody>

                <tr class="grand-total">

                    <td>
                        GRAND TOTAL
                    </td>

                    <td width="120" class="text-right">

                        {{ number_format($grandQty, 2) }}

                    </td>

                    <td width="120" class="text-right">

                        {{ number_format($grandWeight, 2) }}

                    </td>

                    <td width="150" class="text-right">

                        {{ number_format($grandAmount, 2) }}

                    </td>

                </tr>

            </tbody>

        </table>

    @endif


    {{-- AUTO PRINT --}}
    <script>

        window.onload = function () {

            window.print();

        };

    </script>

</body>

</html>

