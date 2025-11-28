<html lang="en">
    <body>
        <div>
            <h2>Item Surat Jalan</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Surat Jalan</th>
                    <th>SKU Produk</th>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryOrderItems as $index => $deliveryOrderItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $deliveryOrderItem->delivery_number }}</td>
                        <td>{{ $deliveryOrderItem->product_sku }}</td>
                        <td>{{ $deliveryOrderItem->product_name }}</td>
                        <td>{{ $deliveryOrderItem->quantity }}</td>
                        <td>{{ $deliveryOrderItem->unit_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
