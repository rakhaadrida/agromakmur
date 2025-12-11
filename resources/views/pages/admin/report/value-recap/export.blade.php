<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Rekap Value - {{ $category->name }}</h2>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
                <tr>
                    <th>No</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Total Stok</th>
                    <th>Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                    <tr class="text-dark">
                        <td>{{ ++$key }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $mapStockByProduct[$product->id] ?? 0 }}</td>
                        <td>{{ $product->total_value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
