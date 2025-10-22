<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Outgoing Items Report</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
                <tr>
                    <th>No</th>
                    <th>Customer</th>
                    <th>Product SKU</th>
                    <th>Product Name</th>
                    <th>Warehouse</th>
                    <th>Qty</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderItems as $key => $orderItem)
                    <tr class="text-dark">
                        <td>{{ ++$key }}</td>
                        <td>{{ $orderItem->customer_name }}</td>
                        <td>{{ $orderItem->product_sku }}</td>
                        <td>{{ $orderItem->product_name }}</td>
                        <td>{{ $orderItem->warehouse_name }}</td>
                        <td>{{ $orderItem->total_quantity }}</td>
                        <td>{{ $orderItem->unit_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
