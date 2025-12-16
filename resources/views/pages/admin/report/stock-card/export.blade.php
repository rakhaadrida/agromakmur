<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Kartu Stok - {{ $product->name }}</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
            <tr>
                <td rowspan="2">No</td>
                <td rowspan="2">Tanggal</td>
                <td rowspan="2">Nomor Transaksi</td>
                <td rowspan="2">Customer / Supplier</td>
                <td rowspan="2">Harga Beli</td>
                <td colspan="2">Hrga Jual</td>
                <td colspan="2">Jumlah</td>
                <td rowspan="2">Sisa Stok</td>
                <td rowspan="2">Admin</td>
            </tr>
            <tr>
                <td>Grosir</td>
                <td>Eceran</td>
                <td>Masuk</td>
                <td>Keluar</td>
            </tr>
            </thead>
            <tbody>
            @if($stockLogs->count() > 0)
                <tr>
                    <td colspan=7">Stok Awal</td>
                    <td>{{ $initialStock }}</td>
                    <td colspan="3"></td>
                </tr>
                @php $currentStock = $initialStock; @endphp
                @foreach($stockLogs as $index => $stockLog)
                    @php $currentStock += $stockLog->quantity; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ !isManualLog($stockLog->type) ? $stockLog->subject->date : $stockLog->subject_date }}</td>
                        <td>{{ !isManualLog($stockLog->type) ? $stockLog->subject->number : '' }}</td>
                        <td>
                            @if(isSupplierLog($stockLog->type)) {{ $stockLog->supplier_name }} @elseif(isCustomerLog($stockLog->type)) {{ $stockLog->customer_name }} @else - @endif
                        </td>
                        <td>{{ isGoodsReceiptLog($stockLog->type) ? $stockLog->price : '' }}</td>
                        <td>{{ isGoodsReceiptLog($stockLog->type) ? $stockLog->wholesale_price : '' }}</td>
                        <td>{{ isGoodsReceiptLog($stockLog->type) ? $stockLog->retail_price : '' }}</td>
                        <td>{{ $stockLog->quantity >= 0 ? $stockLog->quantity : '' }}</td>
                        <td>{{ $stockLog->quantity < 0 ? $stockLog->quantity * -1 : '' }}</td>
                        <td>{{ $currentStock }}</td>
                        <td>{{ $stockLog->user->username }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7">Total</td>
                    <td>{{ $initialStock + $totalIncomingQuantity }}</td>
                    <td>{{ $totalOutgoingQuantity * -1 }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="7">Stok Akhir</td>
                    <td>{{ $initialStock + $totalIncomingQuantity - ($totalOutgoingQuantity * -1) }}</td>
                    <td colspan="3"></td>
                </tr>
            @else
                <tr>
                    <td colspan="11">Tidak Ada Data</td>
                </tr>
            @endif
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
