<html lang="en">
    <body>
        <div>
            <h2>Rekap Penjualan Per {{ $subjectLabel }}</h2>
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
                        <th>Customer</th>
                        <th>Total Faktur</th>
                        <th>Subtotal</th>
                        <th>PPN</th>
                        <th>Grand Total</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @if(isSubjectProduct($subject))
                    @foreach($salesItems as $index => $salesItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $salesItem->product_sku }}</td>
                            <td>{{ $salesItem->product_name }}</td>
                            <td>{{ $salesItem->invoice_count }}</td>
                            <td>{{ $salesItem->total_quantity }}</td>
                            <td>{{ $salesItem->unit_name }}</td>
                            <td>{{ $salesItem->grand_total }}</td>
                        </tr>
                    @endforeach
                @elseif(isSubjectCustomer($subject))
                    @foreach($salesItems as $index => $salesItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $salesItem->customer_name }}</td>
                            <td>{{ $salesItem->invoice_count }}</td>
                            <td>{{ $salesItem->subtotal }}</td>
                            <td>{{ $salesItem->tax_amount }}</td>
                            <td>{{ $salesItem->grand_total }}</td>
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
