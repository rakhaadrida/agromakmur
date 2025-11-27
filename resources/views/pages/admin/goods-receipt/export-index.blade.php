<html lang="en">
    <body>
        <div>
            <h2>Daftar Barang Masuk</h2>
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
                    <th>Cabang</th>
                    <th>Supplier</th>
                    <th>Gudang</th>
                    <th>Tempo</th>
                    <th>Umur Nota</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($goodsReceipts as $index => $goodsReceipt)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $goodsReceipt->number }}</td>
                        <td>{{ $goodsReceipt->date }}</td>
                        <td>{{ $goodsReceipt->branch_name }}</td>
                        <td>{{ $goodsReceipt->supplier_name }}</td>
                        <td>{{ $goodsReceipt->warehouse_name }}</td>
                        <td>{{ $goodsReceipt->tempo }} Day(s)</td>
                        <td>{{ getInvoiceAge($goodsReceipt->date, $goodsReceipt->tempo) }} Day(s)</td>
                        <td>{{ $goodsReceipt->grand_total }}</td>
                        <td>{{ getGoodsReceiptStatusLabel($goodsReceipt->status) }}</td>
                        <td>{{ $goodsReceipt->user_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
