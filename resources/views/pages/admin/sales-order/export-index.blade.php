<html lang="en">
    <body>
        <div>
            <h2>Sales Order Data</h2>
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
                    <th>Customer</th>
                    <th>Marketing</th>
                    <th>Tempo</th>
                    <th>Invoice Age</th>
                    <th>Grand Total</th>
                    <th>Type</th>
                    <th>Note</th>
                    <th>Status</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrders as $index => $salesOrder)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $salesOrder->number }}</td>
                        <td>{{ formatDate($salesOrder->date, 'd M Y') }}</td>
                        <td>{{ $salesOrder->customer_name }}</td>
                        <td>{{ $salesOrder->marketing_name }}</td>
                        <td>{{ $salesOrder->tempo }} Day(s)</td>
                        <td>{{ getInvoiceAge($salesOrder->date, $salesOrder->tempo) }} Day(s)</td>
                        <td>{{ $salesOrder->grand_total }}</td>
                        <td>{{ getSalesOrderTypeLabel($salesOrder->type) }}</td>
                        <td>{{ $salesOrder->note }}</td>
                        <td>{{ getSalesOrderStatusLabel($salesOrder->status) }}</td>
                        <td>{{ $salesOrder->user_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
