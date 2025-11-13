<html lang="en">
    <body>
        <div>
            <h2>Goods Receipt Data</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Number</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Supplier</th>
                    <th>Warehouse</th>
                    <th>Tempo</th>
                    <th>Invoice Age</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($goodsReceipts as $index => $goodsReceipt)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $goodsReceipt->number }}</td>
                        <td>{{ $goodsReceipt->date }}</td>
                        <td>{{ $goodsReceipt->branch_name }}</td>
                        <td>{{ $goodsReceipt->supplier_name }}</td>
                        <td>{{ $goodsReceipt->warehouse_name }}</td>
                        <td>{{ $goodsReceipt->tempo }} Day(s)</td>
                        <td>{{ getInvoiceAge($goodsReceipt->date, $goodsReceipt->tempo) }} Day(s)</td>
                        <td>{{ $goodsReceipt->grand_total }}</td>
                        <td>{{ getGoodsReceiptStatusLabel($goodsReceipt->status) }}</td>
                        <td>{{ $goodsReceipt->user_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
