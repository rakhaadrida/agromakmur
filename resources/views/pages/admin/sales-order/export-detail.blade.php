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
                    <th rowspan="2">No</th>
                    <th rowspan="2">Nomor Order</th>
                    <th rowspan="2">SKU Produk</th>
                    <th rowspan="2">Nama Produk</th>
                    <th rowspan="2">Qty</th>
                    <th rowspan="2">Unit</th>
                    <th rowspan="2">Harga</th>
                    <th rowspan="2">Total</th>
                    <th colspan="2">Diskon</th>
                    <th rowspan="2">Netto</th>
                </tr>
                <tr>
                    <th>%</th>
                    <th>Rupiah</th>
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
                        <td>{{ $salesOrderItem->discount }}</td>
                        <td>{{ $salesOrderItem->discount_amount }}</td>
                        <td>{{ $salesOrderItem->final_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
