<html lang="en">
    <body>
        <div>
            <h2>Item Histori Produk</h2>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name Produk</th>
                    <th>Tanggal BM</th>
                    <th>Nomor BM</th>
                    <th>Supplier</th>
                    <th>Cabang</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Upah</th>
                    <th>Ongkos Kirim</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receiptItems as $index => $receiptItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $receiptItem->product_name }}</td>
                        <td>{{ $receiptItem->receipt_date }}</td>
                        <td>{{ $receiptItem->receipt_number }}</td>
                        <td>{{ $receiptItem->supplier_name }}</td>
                        <td>{{ $receiptItem->branch_name }}</td>
                        <td>{{ $receiptItem->price }}</td>
                        <td>{{ $receiptItem->quantity }}</td>
                        <td>{{ $receiptItem->unit_name }}</td>
                        <td>{{ $receiptItem->wages }}</td>
                        <td>{{ $receiptItem->shipping_cost }}</td>
                        <td>{{ $receiptItem->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
