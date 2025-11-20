<html lang="en">
    <body>
        <div>
            <h2>Plan Order Data</h2>
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
                    <th>Total Items</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($planOrders as $index => $planOrder)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $planOrder->number }}</td>
                        <td>{{ $planOrder->date }}</td>
                        <td>{{ $planOrder->branch_name }}</td>
                        <td>{{ $planOrder->supplier_name }}</td>
                        <td>{{ $planOrder->planOrderItems->count() }}</td>
                        <td>{{ $planOrder->grand_total }}</td>
                        <td>{{ getPlanOrderStatusLabel($planOrder->status) }}</td>
                        <td>{{ $planOrder->user_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
