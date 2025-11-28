<html lang="en">
    <body>
        <div>
            <h2>Item Plan Order</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor PO</th>
                    <th>SKU Produk</th>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($planOrderItems as $index => $planOrderItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $planOrderItem->plan_order_number }}</td>
                        <td>{{ $planOrderItem->product_sku }}</td>
                        <td>{{ $planOrderItem->product_name }}</td>
                        <td>{{ $planOrderItem->quantity }}</td>
                        <td>{{ $planOrderItem->unit_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
