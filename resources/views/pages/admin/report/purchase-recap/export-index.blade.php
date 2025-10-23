<html lang="en">
    <body>
        <div>
            <h2>Purchase Recap By {{ $subjectLabel }}</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                @if(isSubjectProduct($subject))
                    <tr>
                        <th>No</th>
                        <th>Product SKU</th>
                        <th>Product Name</th>
                        <th>Invoice Count</th>
                        <th>Total Quantity</th>
                        <th>Unit</th>
                        <th>Grand Total</th>
                    </tr>
                @else
                    <tr>
                        <th>No</th>
                        <th>Supplier</th>
                        <th>Invoice Count</th>
                        <th>Subtotal</th>
                        <th>Tax Amount</th>
                        <th>Grand Total</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @if(isSubjectProduct($subject))
                    @foreach($purchaseItems as $index => $purchaseItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchaseItem->product_sku }}</td>
                            <td>{{ $purchaseItem->product_name }}</td>
                            <td>{{ $purchaseItem->invoice_count }}</td>
                            <td>{{ $purchaseItem->total_quantity }}</td>
                            <td>{{ $purchaseItem->unit_name }}</td>
                            <td>{{ $purchaseItem->grand_total }}</td>
                        </tr>
                    @endforeach
                @elseif(isSubjectSupplier($subject))
                    @foreach($purchaseItems as $index => $purchaseItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchaseItem->supplier_name }}</td>
                            <td>{{ $purchaseItem->invoice_count }}</td>
                            <td>{{ $purchaseItem->subtotal }}</td>
                            <td>{{ $purchaseItem->tax_amount }}</td>
                            <td>{{ $purchaseItem->grand_total }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7">No Data Available</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
