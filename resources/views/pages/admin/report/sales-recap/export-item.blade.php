<html lang="en">
    <body>
        <div>
            <h2>Sales Recap Items By {{ $subjectLabel }}</h2>
            <h5>Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h5>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                @if(isSubjectProduct($subject))
                    <tr>
                        <th>No</th>
                        <th>Product Name</th>
                        <th>Order Date</th>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Discount (%)</th>
                        <th>Discount Amount</th>
                        <th>Final Amount</th>
                    </tr>
                @else
                    <tr>
                        <th>No</th>
                        <th>Customer</th>
                        <th>Order Date</th>
                        <th>Order Number</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Discount (%)</th>
                        <th>Discount Amount</th>
                        <th>Final Amount</th>
                    </tr>
               @endif
            </thead>
            <tbody>
                @if(isSubjectProduct($subject))
                    @foreach($salesItems as $index => $salesItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $salesItem->product_name }}</td>
                            <td>{{ $salesItem->order_date }}</td>
                            <td>{{ $salesItem->order_number }}</td>
                            <td>{{ $salesItem->customer_name }}</td>
                            <td>{{ $salesItem->quantity }}</td>
                            <td>{{ $salesItem->unit_name }}</td>
                            <td>{{ $salesItem->price }}</td>
                            <td>{{ $salesItem->total }}</td>
                            <td>{{ $salesItem->discount }}</td>
                            <td>{{ $salesItem->discount_amount }}</td>
                            <td>{{ $salesItem->final_amount }}</td>
                        </tr>
                    @endforeach
                @elseif(isSubjectCustomer($subject))
                    @foreach($salesItems as $index => $salesItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $salesItem->customer_name }}</td>
                            <td>{{ $salesItem->order_date }}</td>
                            <td>{{ $salesItem->order_number }}</td>
                            <td>{{ $salesItem->product_name }}</td>
                            <td>{{ $salesItem->quantity }}</td>
                            <td>{{ $salesItem->unit_name }}</td>
                            <td>{{ $salesItem->price }}</td>
                            <td>{{ $salesItem->total }}</td>
                            <td>{{ $salesItem->discount }}</td>
                            <td>{{ $salesItem->discount_amount }}</td>
                            <td>{{ $salesItem->final_amount }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="12">No Data Available</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
