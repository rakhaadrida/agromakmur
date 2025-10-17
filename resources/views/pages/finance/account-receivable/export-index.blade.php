<html lang="en">
    <body>
        <div>
            <h2>Account Receivable Data</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Customer</th>
                    <th>Invoice Count</th>
                    <th>Grand Total</th>
                    <th>Payment</th>
                    <th>Return Amount</th>
                    <th>Outstanding Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accountReceivables as $index => $accountReceivable)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $accountReceivable->customer_name }}</td>
                        <td>{{ $accountReceivable->invoice_count }}</td>
                        <td>{{ $accountReceivable->grand_total ?? 0 }}</td>
                        <td>{{ $accountReceivable->payment_amount ?? 0 }}</td>
                        <td>{{ $accountReceivable->return_amount ?? 0 }}</td>
                        <td>{{ $accountReceivable->outstanding_amount ?? 0 }}</td>
                        <td>{{ getAccountReceivableStatusLabel($accountReceivable->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
