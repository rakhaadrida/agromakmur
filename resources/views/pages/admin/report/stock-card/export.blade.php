<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Kartu Stok - {{ $product->name }}</h2>
            <h5>Tanggal Laporan : {{ formatDateIso($startDate, 'D MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
            <tr>
                <td rowspan="2">No</td>
                <td rowspan="2">Tanggal</td>
                <td rowspan="2">Nomor Transaksi</td>
                <td rowspan="2">Tipe</td>
                <td rowspan="2">Customer / Supplier</td>
                <td colspan="3">Masuk</td>
                <td colspan="3">Keluar</td>
                <td rowspan="2">Admin</td>
            </tr>
            <tr>
                <td>{{ $product ? $product->unit->name : 'Unit' }}</td>
                <td>Gudang</td>
                <td>Jumlah</td>
                <td>{{ $product ? $product->unit->name : 'Unit' }}</td>
                <td>Gudang</td>
                <td>Jumlah</td>
            </tr>
            </thead>
            <tbody>
            @if($stockLogs->count() > 0)
                <tr>
                    <td colspan="5">Stok Awal</td>
                    <td>{{ formatQuantity($initialStock) }}</td>
                    <td colspan="6"></td>
                </tr>
                @foreach($stockLogs as $index => $stockLog)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ !isManualLog($stockLog->type) ? $stockLog->subject->date : $stockLog->subject_date }}</td>
                        <td>{{ !isManualLog($stockLog->type) ? $stockLog->subject->number : '' }}</td>
                        <td>{{ getProductStockLogTypeLabel($stockLog->type) }}</td>
                        <td>
                            @if(isSupplierLog($stockLog->type)) {{ $stockLog->supplier_name }} @elseif(isCustomerLog($stockLog->type)) {{ $stockLog->customer_name }} @else - @endif
                        </td>
                        <td>{{ $stockLog->quantity >= 0 ? $stockLog->quantity : '' }}</td>
                        <td>{{ $stockLog->quantity >= 0 ? $stockLog->warehouse->name : '' }}</td>
                        <td>{{ $stockLog->quantity >= 0 ? $stockLog->final_amount : '' }}</td>
                        <td>{{ $stockLog->quantity < 0 ? $stockLog->quantity * -1 : '' }}</td>
                        <td>{{ $stockLog->quantity < 0 ? $stockLog->warehouse_name : '' }}</td>
                        <td>{{ $stockLog->quantity < 0 ? $stockLog->final_amount : '' }}</td>
                        <td>{{ $stockLog->user->username }} - {{ formatDate($stockLog->subject->created_at, 'H:i:s') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5">Total</td>
                    <td>{{ $initialStock + $totalIncomingQuantity }}</td>
                    <td colspan="2"></td>
                    <td>{{ $totalOutgoingQuantity * -1 }}</td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="5">Stok Akhir</td>
                    <td>{{ $initialStock + $totalIncomingQuantity - ($totalOutgoingQuantity * -1) }}</td>
                    <td colspan="6"></td>
                </tr>
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
