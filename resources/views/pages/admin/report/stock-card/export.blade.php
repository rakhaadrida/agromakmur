<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Stock Card - {{ $product->name }}</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
            <tr>
                <td rowspan="2">No</td>
                <td rowspan="2">Date</td>
                <td rowspan="2">Transaction Number</td>
                <td rowspan="2">Type</td>
                <td rowspan="2">Customer / Supplier</td>
                <td colspan="3">Incoming</td>
                <td colspan="3">Outgoing</td>
                <td rowspan="2">Admin</td>
            </tr>
            <tr>
                <td>{{ $product ? $product->unit->name : 'Unit' }}</td>
                <td>Warehouse</td>
                <td>Amount</td>
                <td>{{ $product ? $product->unit->name : 'Unit' }}</td>
                <td>Warehouse</td>
                <td>Amount</td>
            </tr>
            </thead>
            <tbody>
            @if($stockLogs->count() > 0)
                <tr>
                    <td colspan="5">Initial Stock</td>
                    <td>{{ formatQuantity($initialStock) }}</td>
                    <td colspan="6"></td>
                </tr>
                @foreach($stockLogs as $index => $stockLog)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $stockLog->subject->date }}</td>
                        <td>{{ $stockLog->subject->number }}</td>
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
                    <td colspan="5">Final Stock</td>
                    <td>{{ $initialStock + $totalIncomingQuantity - ($totalOutgoingQuantity * -1) }}</td>
                    <td colspan="6"></td>
                </tr>
            @else
                <tr>
                    <td colspan="12">No Data Available</td>
                </tr>
            @endif
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
