<html lang="en">
    <body>
        <div>
            <h2>Daftar Item Piutang</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'D MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Customer</th>
                    <th>Nomor SO</th>
                    <th>Tanggal SO</th>
                    <th>Jatuh Tempo</th>
                    <th>Umur Nota</th>
                    <th>Cabang</th>
                    <th>Tipe</th>
                    <th>Grand Total</th>
                    <th>Sudah Dibayar</th>
                    <th>Jumlah Retur</th>
                    <th>Belum Dibayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receivableItems as $index => $receivableItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $receivableItem->customer_name }}</td>
                        <td>{{ $receivableItem->number }}</td>
                        <td>{{ $receivableItem->date }}</td>
                        <td>{{ getDueDate($receivableItem->date, $receivableItem->tempo, 'DD-MM-Y') }}</td>
                        <td>{{ getInvoiceAge($receivableItem->date, $receivableItem->tempo) }} Hari</td>
                        <td>{{ $receivableItem->branch_name }}</td>
                        <td>{{ getSalesOrderTypeLabel($receivableItem->type) }}</td>
                        <td>{{ $receivableItem->grand_total ?? 0 }}</td>
                        <td>{{ $receivableItem->payment_amount ?? 0 }}</td>
                        <td>{{ $receivableItem->return_amount ?? 0 }}</td>
                        <td>{{ $receivableItem->outstanding_amount ?? 0 }}</td>
                        <td>{{ getAccountReceivableStatusLabel($receivableItem->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
