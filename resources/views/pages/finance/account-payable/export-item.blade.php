<html lang="en">
    <body>
        <div>
            <h2>Daftar Item Hutang</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'D MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Supplier</th>
                    <th>Nomor BM</th>
                    <th>Tanggal BM</th>
                    <th>Jatuh Tempo</th>
                    <th>Umur Nota</th>
                    <th>Cabang</th>
                    <th>Grand Total</th>
                    <th>Sudah Dibayar</th>
                    <th>Jumlah Retur</th>
                    <th>Belum Dibayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payableItems as $index => $payableItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $payableItem->supplier_name }}</td>
                        <td>{{ $payableItem->number }}</td>
                        <td>{{ $payableItem->date }}</td>
                        <td>{{ getDueDate($payableItem->date, $payableItem->tempo, 'DD-MM-Y') }}</td>
                        <td>{{ getInvoiceAge($payableItem->date, $payableItem->tempo) }} Hari</td>
                        <td>{{ $payableItem->branch_name }}</td>
                        <td>{{ $payableItem->grand_total ?? 0 }}</td>
                        <td>{{ $payableItem->payment_amount ?? 0 }}</td>
                        <td>{{ $payableItem->return_amount ?? 0 }}</td>
                        <td>{{ $payableItem->outstanding_amount ?? 0 }}</td>
                        <td>{{ getAccountPayableStatusLabel($payableItem->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
