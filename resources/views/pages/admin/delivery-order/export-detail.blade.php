<html lang="en">
    <body>
        <div>
            <h2>Delivery Order Items</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Delivery Number</th>
                    <th>Product SKU</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryOrderItems as $index => $deliveryOrderItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $deliveryOrderItem->delivery_number }}</td>
                        <td>{{ $deliveryOrderItem->product_sku }}</td>
                        <td>{{ $deliveryOrderItem->product_name }}</td>
                        <td>{{ $deliveryOrderItem->quantity }}</td>
                        <td>{{ $deliveryOrderItem->unit_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
