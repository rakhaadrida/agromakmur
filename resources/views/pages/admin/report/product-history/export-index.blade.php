<html lang="en">
    <body>
        <div>
            <h2>Product History Report</h2>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Latest Branch</th>
                    <th>Latest Supplier</th>
                    <th>Latest Receipt Date</th>
                    <th>Latest Receipt Number</th>
                    <th>Latest Price</th>
                    <th>Latest Qty</th>
                    <th>Latest Unit</th>
                    <th>Latest Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->product_sku }}</td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->latest_branch }}</td>
                        <td>{{ $product->latest_supplier }}</td>
                        <td>{{ $product->latest_date }}</td>
                        <td>{{ $product->latest_number }}</td>
                        <td>{{ $product->latest_price }}</td>
                        <td>{{ $product->latest_quantity }}</td>
                        <td>{{ $product->latest_unit }}</td>
                        <td>{{ $product->latest_price * $product->latest_quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
