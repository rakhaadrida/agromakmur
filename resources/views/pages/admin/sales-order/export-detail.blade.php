<html lang="en">
    <body>
        <div>
            <h2>Item Sales Order</h2>
            <h5>Tanggal Laporan : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Order</th>
                    <th>SKU Produk</th>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrderItems as $index => $salesOrderItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $salesOrderItem->order_number }}</td>
                        <td>{{ $salesOrderItem->product_sku }}</td>
                        <td>{{ $salesOrderItem->product_name }}</td>
                        <td>{{ $salesOrderItem->quantity }}</td>
                        <td>{{ $salesOrderItem->unit_name }}</td>
                        <td>{{ $salesOrderItem->price }}</td>
                        <td>{{ $salesOrderItem->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
