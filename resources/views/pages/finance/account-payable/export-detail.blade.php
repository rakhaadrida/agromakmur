<html lang="en">
    <body>
        <div>
            <h2>Account Payable - {{ $supplier->name }}</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Receipt Number</th>
                    <th>Receipt Date</th>
                    <th>Due Date</th>
                    <th>Invoice Age</th>
                    <th>Branch</th>
                    <th>Grand Total</th>
                    <th>Payment</th>
                    <th>Return Amount</th>
                    <th>Unpaid Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payableItems as $index => $payableItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $payableItem->number }}</td>
                        <td>{{ $payableItem->date }}</td>
                        <td>{{ getDueDate($payableItem->date, $payableItem->tempo, 'd-m-Y') }}</td>
                        <td>{{ getInvoiceAge($payableItem->date, $payableItem->tempo) }} Hari</td>
                        <td>{{ $payableItem->branch_name }}</td>
                        <td>{{ $payableItem->grand_total ?? 0 }}</td>
                        <td>{{ $payableItem->payment_amount ?? 0 }}</td>
                        <td>{{ $payableItem->return_amount ?? 0 }}</td>
                        <td>{{ $payableItem->outstanding_amount ?? 0 }}</td>
                        <td>{{ getAccountPayableStatusLabel($payableItem->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
