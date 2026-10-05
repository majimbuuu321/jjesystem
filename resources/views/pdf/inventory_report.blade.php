<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background: #f5f5f5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Inventory Report</h2>

    <p>
        {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
        -
        {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
    </p>
</div>

@forelse($records as $record)

    <table style="margin-bottom:10px;">
        <tr>
            <td>
                <strong>Inventory No:</strong>
                {{ $record->inventory_no }}
            </td>

            <td>
                <strong>Date:</strong>
                {{ $record->created_at->format('M d, Y') }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Transfer From:</strong>

                {{
                    $record->warehouseFrom?->warehouse_name
                    ?? $record->supplierFrom?->company_name
                    ?? '-'
                }}
            </td>

            <td>
                <strong>Transfer To:</strong>

                @if($record->inventory_type == 4)

                    {{ $record->supplierTo?->company_name ?? '-' }}

                @else

                    {{ $record->warehouseTo?->warehouse_name ?? '-' }}

                @endif
            </td>
        </tr>
    </table>

    <table style="margin-bottom:25px;">
        <thead>
        <tr>
            <th>Product Code</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>UOM</th>
        </tr>
        </thead>

        <tbody>

        @foreach($record->InventoryDetail as $item)

            <tr>

                <td>
                    {{ $item->product?->product_code }}
                </td>

                <td>
                    {{ $item->product?->product_name }}
                </td>

                <td>
                    {{ number_format($item->quantity, 2) }}
                </td>

                <td>
                    {{ $item->unitOfMeasurement?->unit_code }}
                </td>

            </tr>

        @endforeach

        </tbody>
    </table>

@empty

    <p>No inventory records found.</p>

@endforelse

</body>
</html>