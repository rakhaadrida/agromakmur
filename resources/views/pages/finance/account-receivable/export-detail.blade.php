<html lang="en">
    <body>
        <div>
            <h2>Account Receivable - {{ $customer->name }}</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Order Number</th>
                    <th>Order Date</th>
                    <th>Due Date</th>
                    <th>Invoice Age</th>
                    <th>Branch</th>
                    <th>Type</th>
                    <th>Grand Total</th>
                    <th>Payment</th>
                    <th>Return Amount</th>
                    <th>Unpaid Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receivableItems as $index => $receivableItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $receivableItem->number }}</td>
                        <td>{{ $receivableItem->date }}</td>
                        <td>{{ getDueDate($receivableItem->date, $receivableItem->tempo, 'd-m-Y') }}</td>
                        <td>{{ getInvoiceAge($receivableItem->date, $receivableItem->tempo) }} Day(s)</td>
                        <td>{{ $receivableItem->branch_name }}</td>
                        <td>{{ getSalesOrderTypeLabel($receivableItem->type) }}</td>
                        <td>{{ $receivableItem->grand_total ?? 0 }}</td>
                        <td>{{ $receivableItem->payment_amount ?? 0 }}</td>
                        <td>{{ $receivableItem->return_amount ?? 0 }}</td>
                        <td>{{ $receivableItem->outstanding_amount ?? 0 }}</td>
                        <td>{{ getAccountReceivableStatusLabel($receivableItem->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
