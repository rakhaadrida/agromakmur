<html lang="en">
    <body>
        <div>
            <h2>Sales Order Items</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Order Number</th>
                    <th rowspan="2">Product SKU</th>
                    <th rowspan="2">Product Name</th>
                    <th rowspan="2">Qty</th>
                    <th colspan="{{ $totalWarehouses }}">Warehouse</th>
                    <th rowspan="2">Unit</th>
                    <th rowspan="2">Price</th>
                    <th rowspan="2">Total</th>
                    <th colspan="2">Discount</th>
                    <th rowspan="2">Final Amount</th>
                </tr>
                <tr>
                    @foreach($warehouses as $key => $warehouse)
                        <th>{{ $warehouse->name }}</th>
                    @endforeach
                    <th>%</th>
                    <th>Rupiah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrderItems as $index => $salesOrderItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $salesOrderItem->order_number }}</td>
                        <td>{{ $salesOrderItem->product_sku }}</td>
                        <td>{{ $salesOrderItem->product_name }}</td>
                        <td>{{ $salesOrderItem->quantity }}</td>
                        @foreach($warehouses as $warehouse)
                            <td>{{ $productWarehouses[$salesOrderItem->sales_order_id][$salesOrderItem->product_id][$warehouse->id] ?? '' }}</td>
                        @endforeach
                        <td>{{ $salesOrderItem->unit_name }}</td>
                        <td>{{ $salesOrderItem->price }}</td>
                        <td>{{ $salesOrderItem->total }}</td>
                        <td>{{ $salesOrderItem->discount }}</td>
                        <td>{{ $salesOrderItem->discount_amount }}</td>
                        <td>{{ $salesOrderItem->final_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
