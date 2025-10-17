<html lang="en">
    <body>
        <div>
            <h2>Delivery Order Data</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Number</th>
                    <th>Date</th>
                    <th>Invoice Number</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryOrders as $index => $deliveryOrder)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $deliveryOrder->number }}</td>
                        <td>{{ formatDate($deliveryOrder->date, 'd M Y') }}</td>
                        <td>{{ $deliveryOrder->sales_order_number }}</td>
                        <td>{{ $deliveryOrder->customer_name }}</td>
                        <td>{{ getDeliveryOrderStatusLabel($deliveryOrder->status) }}</td>
                        <td>{{ $deliveryOrder->user_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
