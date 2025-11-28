<html lang="en">
    <body>
        <div>
            <h2>Daftar Plan Order</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor PO</th>
                    <th>Tanggal</th>
                    <th>Cabang</th>
                    <th>Supplier</th>
                    <th>Total Barang</th>
                    <th>Status</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($planOrders as $index => $planOrder)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $planOrder->number }}</td>
                        <td>{{ $planOrder->date }}</td>
                        <td>{{ $planOrder->branch_name }}</td>
                        <td>{{ $planOrder->supplier_name }}</td>
                        <td>{{ $planOrder->planOrderItems->count() }}</td>
                        <td>{{ getPlanOrderStatusLabel($planOrder->status) }}</td>
                        <td>{{ $planOrder->user_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
