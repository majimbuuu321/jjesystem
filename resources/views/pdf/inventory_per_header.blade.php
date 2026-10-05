<style>

        .header, .footer {
            text-align: center;
            font-weight:bold;
        }

      
        .details {
            margin-bottom: 30px;
            font-size: 14px;
        }

        .details table {
            width: 100%;
            margin-top: 10px;
        }

        .details td {
            padding: 4px 8px;
        }

        .items-table-container {
            overflow-x: auto;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th, .items-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }

        .items-table th {
            background-color: #f0f0f0;
        }

        .total {
            margin-top: 20px;
            margin-right:5px;
            text-align: right;
        }
    </style>
    

    <div class="header">
        <h1>JJE Enterprises</h1>
        <p>Inventory</p>
    </div>

    <div class="details">
        <table>
            <tr>
                <td><strong>Document No:</strong> {{ $record->document_no }}</td>
            </tr>
            <tr>
                <td><strong>Transfer Date:</strong> {{ $record->transfer_date }}</td>
                <td><strong>Inventory Type:</strong> {{ $record->inventory_type->inventory_type }}</td>
                <td><strong>Plate No:</strong> {{ $record->plate_no }}</td>
            </tr>
            <tr>
                <td><strong>Transfer From:</strong> {{ $record->warehouseFrom?->warehouse_name }}</td>
                <td><strong>Transfer To:</strong> {{ $record->warehouseTo?->warehouse_name }}</td>
            </tr>
            <tr>
                <td><strong>Assigned From:</strong> {{ $record->assigned_from }}</td>
                <td><strong>Assigned To:</strong> {{ $record->assigned_to }}</td>
            </tr>
        </table>
    </div>

     <div class="items-table-container">
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Weight(kg)</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Total Cost</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                        <td>{{ $products[$index]->product_code }}</td>
                        <td>{{ $products[$index]->product_description }}</td>
                        <td>{{ $uom[$index]->unit_code }}</td>
                        <td>{{ $item->weight }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit_cost }}</td>
                        <td>{{ $item->gross_amount }}</td>
                        <td>{{ $item->remarks }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

     @php
        $totalCost = $items->sum(fn($cost) => $cost->gross_amount);
    @endphp

    <div class="total">
        <h1>
            Total Cost: ₱
            {{
                number_format(
                    $totalCost,2
                )
            }}
           
        </h1>
    </div>



 