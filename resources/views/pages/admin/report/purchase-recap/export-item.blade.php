<html lang="en">
    <body>
        <div>
            <h2>Rekap Item Pembelian Per {{ $subjectLabel }}</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                @if(isSubjectProduct($subject))
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Tanggal BM</th>
                        <th>Nomor BM</th>
                        <th>Supplier</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Harga</th>
                        <th>Upah</th>
                        <th>Ongkos Kirim</th>
                        <th>Harga Modal</th>
                        <th>Total</th>
                    </tr>
                @else
                    <tr>
                        <th>No</th>
                        <th>Supplier</th>
                        <th>Tanggal BM</th>
                        <th>Nomor BM</th>
                        <th>Nama Produk</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Harga</th>
                        <th>Upah</th>
                        <th>Ongkos Kirim</th>
                        <th>Total</th>
                    </tr>
               @endif
            </thead>
            <tbody>
                @if(isSubjectProduct($subject))
                    @foreach($purchaseItems as $index => $purchaseItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchaseItem->product_name }}</td>
                            <td>{{ $purchaseItem->receipt_date }}</td>
                            <td>{{ $purchaseItem->receipt_number }}</td>
                            <td>{{ $purchaseItem->supplier_name }}</td>
                            <td>{{ $purchaseItem->quantity }}</td>
                            <td>{{ $purchaseItem->unit_name }}</td>
                            <td>{{ $purchaseItem->price }}</td>
                            <td>{{ $purchaseItem->wages }}</td>
                            <td>{{ $purchaseItem->shipping_cost }}</td>
                            <td>{{ $purchaseItem->cost_price }}</td>
                            <td>{{ $purchaseItem->total }}</td>
                        </tr>
                    @endforeach
                @elseif(isSubjectSupplier($subject))
                    @foreach($purchaseItems as $index => $purchaseItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchaseItem->supplier_name }}</td>
                            <td>{{ $purchaseItem->receipt_date }}</td>
                            <td>{{ $purchaseItem->receipt_number }}</td>
                            <td>{{ $purchaseItem->product_name }}</td>
                            <td>{{ $purchaseItem->quantity }}</td>
                            <td>{{ $purchaseItem->unit_name }}</td>
                            <td>{{ $purchaseItem->price }}</td>
                            <td>{{ $purchaseItem->wages }}</td>
                            <td>{{ $purchaseItem->shipping_cost }}</td>
                            <td>{{ $purchaseItem->cost_price }}</td>
                            <td>{{ $purchaseItem->total }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="12">Tidak Ada Data</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
