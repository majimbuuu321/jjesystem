<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Inventory Report</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0;
        }

        .period {
            margin-bottom: 20px;
        }

        .inventory {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .inventory-header {
            background: #f1f1f1;
            border: 1px solid #ccc;
            padding: 8px;
            margin-bottom: 5px;
        }

        .inventory-header table {
            width: 100%;
            border-collapse: collapse;
        }

        .inventory-header td {
            padding: 3px;
        }

        table.details {
            width: 100%;
            border-collapse: collapse;
        }

        table.details th,
        table.details td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        table.details th {
            background: #f1f1f1;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 40px;
        }
    </style>
</head>

<body>

<div class="header">
    <h1>JJE INVENTORY REPORT</h1>

    <p>
        {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
        -
        {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
    </p>
    <p>
        <strong>Status:</strong>
        {{ ucfirst($status) }}
    </p>
</div>

@if($records->isEmpty())

    <p style="text-align: center;">
        No inventory records found for the selected date range.
    </p>

@else

    @foreach($records as $record)

        <div class="inventory">

            <div class="inventory-header">

                <table>
                    <tr>
                        <td width="20%">
                            <strong>Document No.</strong>
                        </td>

                        <td width="30%">
                            {{ $record->document_no ?? $record->document_no }}
                        </td>

                        <td width="20%">
                            <strong>Transfer Date</strong>
                        </td>

                        <td width="30%">
                           {{ $record->transfer_date
                            ? \Carbon\Carbon::parse($record->transfer_date)->format('M d, Y')
                            : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="20%">
                            <strong>Inventory Type</strong>
                        </td>

                        <td width="30%">
                            {{ $record->inventory_type->inventory_type }}
                        </td>

                        <td width="20%">
                            <strong>Plate No.</strong>
                        </td>

                        <td width="30%">
                          {{ $record->plate_no }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Transfer From</strong>
                        </td>

                        <td>
                            @if($record->warehouseFrom)
                                {{ $record->warehouseFrom->warehouse_name }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            <strong>Transfer To</strong>
                        </td>

                        <td>
                            @if($record->inventory_type_id == 4)

                                {{ $record->supplierTo?->company_name ?? '-' }}

                            @else

                                {{ $record->warehouseTo?->warehouse_name ?? '-' }}

                            @endif
                        </td>
                    </tr>

                     <tr>
                        <td>
                            <strong>Assigned From</strong>
                        </td>

                        <td>
                            {{ $record->assigned_from }}
                        </td>

                        <td>
                            <strong>Assigned To</strong>
                        </td>

                        <td>
                            {{ $record->assigned_to }}
                        </td>
                    </tr>
                </table>

            </div>

            <table class="details">

                <thead>
                    <tr>
                        <th width="15%">Product Code</th>
                        <th width="45%">Product</th>
                        <th width="15%">Quantity</th>
                        <th width="15%">UOM</th>
                        <th width="15%">Unit Cost</th>
                        <th width="15%">Gross Amount</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($record->InventoryDetail as $detail)

                        <tr>

                            <td>
                                {{ $detail->product?->product_code ?? '-' }}
                            </td>

                            <td>
                                {{ $detail->product?->product_description ?? '-' }}
                            </td>

                            <td class="text-right">
                                {{ number_format($detail->quantity ?? 0, 2) }}
                            </td>

                            <td>
                                {{ $detail->unitOfMeasurement?->unit_code ?? '-' }}
                            </td>

                             <td>
                                {{ $detail->unit_cost ?? '-' }}
                            </td>

                             <td>
                                {{ $detail->gross_amount ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" style="text-align: center;">
                                No inventory details.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    @endforeach

@endif

<div class="footer">

    <table width="100%">
        <tr>
            <td width="50%">
                Prepared By:
                __________________________
            </td>

            <td width="50%">
                Approved By:
                __________________________
            </td>
        </tr>
    </table>

</div>

</body>
</html>