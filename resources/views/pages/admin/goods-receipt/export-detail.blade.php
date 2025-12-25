<html lang="en">
    <body>
        <div>
            <h2>Item Barang Masuk</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'DD MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor BM</th>
                    <th>SKU Produk</th>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Harga</th>
                    <th>Upah</th>
                    <th>Ongkos Kirim</th>
                    <th>Harga Modal</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($goodsReceiptItems as $index => $goodsReceiptItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $goodsReceiptItem->receipt_number }}</td>
                        <td>{{ $goodsReceiptItem->product_sku }}</td>
                        <td>{{ $goodsReceiptItem->product_name }}</td>
                        <td>{{ $goodsReceiptItem->quantity }}</td>
                        <td>{{ $goodsReceiptItem->unit_name }}</td>
                        <td>{{ $goodsReceiptItem->price }}</td>
                        <td>{{ $goodsReceiptItem->wages }}</td>
                        <td>{{ $goodsReceiptItem->shipping_cost }}</td>
                        <td>{{ $goodsReceiptItem->cost_price }}</td>
                        <td>{{ $goodsReceiptItem->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
