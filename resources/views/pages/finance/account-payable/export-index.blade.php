<html lang="en">
    <body>
        <div>
            <h2>Daftar Hutang</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'D MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Supplier</th>
                    <th>Total Faktur</th>
                    <th>Grand Total</th>
                    <th>Sudah Dibayar</th>
                    <th>Jumlah Retur</th>
                    <th>Belum Dibayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accountPayables as $index => $accountPayable)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $accountPayable->supplier_name }}</td>
                        <td>{{ $accountPayable->invoice_count }}</td>
                        <td>{{ $accountPayable->grand_total ?? 0 }}</td>
                        <td>{{ $accountPayable->payment_amount ?? 0 }}</td>
                        <td>{{ $accountPayable->return_amount ?? 0 }}</td>
                        <td>{{ $accountPayable->outstanding_amount ?? 0 }}</td>
                        <td>{{ getAccountPayableStatusLabel($accountPayable->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
