<html lang="en">
    <body>
        <div>
            <h2>Daftar Surat Jalan</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'D MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Nomor SO</th>
                    <th>Cabang</th>
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
                        <td>{{ $deliveryOrder->date }}</td>
                        <td>{{ $deliveryOrder->sales_order_number }}</td>
                        <td>{{ $deliveryOrder->branch_name }}</td>
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
