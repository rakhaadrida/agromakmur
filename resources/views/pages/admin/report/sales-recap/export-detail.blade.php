<html lang="en">
    <body>
        <div>
            <h2>Rekap Penjualan - {{ $item->name }}</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                @if(isSubjectProduct($subject))
                    <tr>
                        <th>No</th>
                        <th>Tanggal SO</th>
                        <th>Nomor SO</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                @else
                    <tr>
                        <th>No</th>
                        <th>Tanggal SO</th>
                        <th>Nomor SO</th>
                        <th>Nama Produk</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @if(isSubjectProduct($subject))
                    @foreach($salesItems as $index => $salesItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $salesItem->order_date }}</td>
                            <td>{{ $salesItem->order_number }}</td>
                            <td>{{ $salesItem->customer_name }}</td>
                            <td>{{ $salesItem->quantity }}</td>
                            <td>{{ $salesItem->unit_name }}</td>
                            <td>{{ $salesItem->price }}</td>
                            <td>{{ $salesItem->total }}</td>
                        </tr>
                    @endforeach
                @elseif(isSubjectCustomer($subject))
                    @foreach($salesItems as $index => $salesItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $salesItem->order_date }}</td>
                            <td>{{ $salesItem->order_number }}</td>
                            <td>{{ $salesItem->product_name }}</td>
                            <td>{{ $salesItem->quantity }}</td>
                            <td>{{ $salesItem->unit_name }}</td>
                            <td>{{ $salesItem->price }}</td>
                            <td>{{ $salesItem->total }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8">Tidak Ada Data</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
