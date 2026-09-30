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
        <p>Purchase Order</p>
    </div>

    <div class="details">
        <table>
            <tr>
                <td><strong>PO No:</strong> {{ $record->purchase_order_no }}</td>
                <td><strong>Invoice No:</strong> {{ $record->invoice_no }}</td>
                <td><strong>Received Date:</strong> {{ $record->received_date }}</td>
            </tr>
            <tr>
                <td><strong>Supplier:</strong> {{ $record->supplier->company_name }}</td>
                <td><strong>Warehouse:</strong> {{ $record->warehouse->warehouse_name }}</td>
                <td><strong>Payment Terms:</strong> {{ $record->paymentTerms->payment_terms }}</td>
            </tr>
        </table>
    </div>

    <div class="items-table-container">
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>UOM</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Total Cost</th>
                    <th>Discount Rate (%)</th>
                    <th>Discount Amount</th>
                    <th>Net Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                        <td>{{ $products[$index]->product_description }}</td>
                        <td>{{ $uom[$index]->unit_code }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit_cost }}</td>
                        <td>{{ $item->total_cost }}</td>
                        <td>{{ $item->discount_rate }}</td>
                        <td>{{ $item->discount_amount }}</td>
                        <td>{{ $item->net_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @php
        $totalCost = $items->sum(fn($cost) => $cost->quantity * $cost->unit_cost);
        $discountAmount = $items->sum(fn($cost) => $cost->discount_amount);
        $netAmount = $items->sum(fn($cost) => $cost->net_amount);
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

        <h1>
            Discount Amount: ₱
            {{
                number_format(
                    $discountAmount,2
                )
            }}
        </h1>

        <h1>
            Net Amount: ₱
            {{
                number_format(
                    $netAmount,2
                )
            }}
        </h1>

    </div>


 