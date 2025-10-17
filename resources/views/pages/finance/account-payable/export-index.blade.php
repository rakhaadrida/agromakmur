<html lang="en">
    <body>
        <div>
            <h2>Account Payable Data</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Supplier</th>
                    <th>Invoice Count</th>
                    <th>Grand Total</th>
                    <th>Payment</th>
                    <th>Return Amount</th>
                    <th>Outstanding Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accountPayables as $index => $accountPayable)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $accountPayable->supplier_name }}</td>
                        <td>{{ $accountPayable->invoice_count }}</td>
                        <td>{{ $accountPayable->grand_total ?? 0 }}</td>
                        <td>{{ $accountPayable->payment_amount ?? 0 }}</td>
                        <td>{{ $accountPayable->return_amount ?? 0 }}</td>
                        <td>{{ $accountPayable->outstanding_amount ?? 0 }}</td>
                        <td>{{ getAccountPayableStatusLabel($accountPayable->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
