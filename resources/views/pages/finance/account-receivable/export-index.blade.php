<html lang="en">
    <body>
        <div>
            <h2>Daftar Piutang</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'D MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Customer</th>
                    <th>Total Faktur</th>
                    <th>Grand Total</th>
                    <th>Sudah Dibayar</th>
                    <th>Jumlah Retur</th>
                    <th>Belum Dibayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accountReceivables as $index => $accountReceivable)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $accountReceivable->customer_name }}</td>
                        <td>{{ $accountReceivable->invoice_count }}</td>
                        <td>{{ $accountReceivable->grand_total ?? 0 }}</td>
                        <td>{{ $accountReceivable->payment_amount ?? 0 }}</td>
                        <td>{{ $accountReceivable->return_amount ?? 0 }}</td>
                        <td>{{ $accountReceivable->outstanding_amount ?? 0 }}</td>
                        <td>{{ getAccountReceivableStatusLabel($accountReceivable->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
