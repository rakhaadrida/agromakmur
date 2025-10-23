<html lang="en">
    <body>
        <div>
            <h2>Purchase Recap - {{ $item->name }}</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                @if(isSubjectProduct($subject))
                    <tr>
                        <th>No</th>
                        <th>Receipt Date</th>
                        <th>Receipt Number</th>
                        <th>Supplier</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Wages</th>
                        <th>Shipping Cost</th>
                        <th>Total</th>
                    </tr>
                @else
                    <tr>
                        <th>No</th>
                        <th>Receipt Date</th>
                        <th>Receipt Number</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Wages</th>
                        <th>Shipping Cost</th>
                        <th>Total</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @if(isSubjectProduct($subject))
                    @foreach($purchaseItems as $index => $purchaseItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchaseItem->receipt_date }}</td>
                            <td>{{ $purchaseItem->receipt_number }}</td>
                            <td>{{ $purchaseItem->supplier_name }}</td>
                            <td>{{ $purchaseItem->quantity }}</td>
                            <td>{{ $purchaseItem->unit_name }}</td>
                            <td>{{ $purchaseItem->price }}</td>
                            <td>{{ $purchaseItem->wages }}</td>
                            <td>{{ $purchaseItem->shipping_cost }}</td>
                            <td>{{ $purchaseItem->total }}</td>
                        </tr>
                    @endforeach
                @elseif(isSubjectSupplier($subject))
                    @foreach($purchaseItems as $index => $purchaseItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchaseItem->receipt_date }}</td>
                            <td>{{ $purchaseItem->receipt_number }}</td>
                            <td>{{ $purchaseItem->product_name }}</td>
                            <td>{{ $purchaseItem->quantity }}</td>
                            <td>{{ $purchaseItem->unit_name }}</td>
                            <td>{{ $purchaseItem->price }}</td>
                            <td>{{ $purchaseItem->wages }}</td>
                            <td>{{ $purchaseItem->shipping_cost }}</td>
                            <td>{{ $purchaseItem->total }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10">No Data Available</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
