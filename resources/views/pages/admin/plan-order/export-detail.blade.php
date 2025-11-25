<html lang="en">
    <body>
        <div>
            <h2>Plan Order Items</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>PO Number</th>
                    <th>Product SKU</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($planOrderItems as $index => $planOrderItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $planOrderItem->plan_order_number }}</td>
                        <td>{{ $planOrderItem->product_sku }}</td>
                        <td>{{ $planOrderItem->product_name }}</td>
                        <td>{{ $planOrderItem->quantity }}</td>
                        <td>{{ $planOrderItem->unit_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
