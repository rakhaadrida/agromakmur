<html lang="en">
    <body>
        <div>
            <h2>Daftar Sales Order</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'DD MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Cabang</th>
                    <th>Customer</th>
                    <th>Sales</th>
                    <th>Tempo</th>
                    <th>Umur Nota</th>
                    <th>Grand Total</th>
                    <th>Tipe</th>
                    <th>Note</th>
                    <th>Status</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrders as $index => $salesOrder)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $salesOrder->number }}</td>
                        <td>{{ $salesOrder->date }}</td>
                        <td>{{ $salesOrder->branch_name }}</td>
                        <td>{{ $salesOrder->customer_name }}</td>
                        <td>{{ $salesOrder->marketing_name }}</td>
                        <td>{{ $salesOrder->tempo }} Hari</td>
                        <td>{{ getInvoiceAge($salesOrder->date, $salesOrder->tempo) }} Hari</td>
                        <td>{{ $salesOrder->grand_total }}</td>
                        <td>{{ getSalesOrderTypeLabel($salesOrder->type) }}</td>
                        <td>{{ $salesOrder->note }}</td>
                        <td>{{ getSalesOrderStatusLabel($salesOrder->status) }}</td>
                        <td>{{ $salesOrder->user_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
