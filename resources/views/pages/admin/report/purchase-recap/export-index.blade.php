<html lang="en">
    <body>
        <div>
            <h2>Rekap Pembelian Per {{ $subjectLabel }}</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                @if(isSubjectProduct($subject))
                    <tr>
                        <th>No</th>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Total Faktur</th>
                        <th>Total Qty</th>
                        <th>Unit</th>
                        <th>Grand Total</th>
                    </tr>
                @else
                    <tr>
                        <th>No</th>
                        <th>Supplier</th>
                        <th>Total Faktur</th>
                        <th>Subtotal</th>
                        <th>PPN</th>
                        <th>Grand Total</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @if(isSubjectProduct($subject))
                    @foreach($purchaseItems as $index => $purchaseItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchaseItem->product_sku }}</td>
                            <td>{{ $purchaseItem->product_name }}</td>
                            <td>{{ $purchaseItem->invoice_count }}</td>
                            <td>{{ $purchaseItem->total_quantity }}</td>
                            <td>{{ $purchaseItem->unit_name }}</td>
                            <td>{{ $purchaseItem->grand_total }}</td>
                        </tr>
                    @endforeach
                @elseif(isSubjectSupplier($subject))
                    @foreach($purchaseItems as $index => $purchaseItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchaseItem->supplier_name }}</td>
                            <td>{{ $purchaseItem->invoice_count }}</td>
                            <td>{{ $purchaseItem->subtotal }}</td>
                            <td>{{ $purchaseItem->tax_amount }}</td>
                            <td>{{ $purchaseItem->grand_total }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7">Tidak Ada Data</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
