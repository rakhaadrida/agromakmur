<html lang="en">
    <body>
        <div>
            <h2>Product History Items</h2>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Receipt Date</th>
                    <th>Receipt Number</th>
                    <th>Supplier</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Wages</th>
                    <th>Shipping Cost</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receiptItems as $index => $receiptItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $receiptItem->product_name }}</td>
                        <td>{{ $receiptItem->receipt_date }}</td>
                        <td>{{ $receiptItem->receipt_number }}</td>
                        <td>{{ $receiptItem->supplier_name }}</td>
                        <td>{{ $receiptItem->price }}</td>
                        <td>{{ $receiptItem->quantity }}</td>
                        <td>{{ $receiptItem->unit_name }}</td>
                        <td>{{ $receiptItem->wages }}</td>
                        <td>{{ $receiptItem->shipping_cost }}</td>
                        <td>{{ $receiptItem->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
