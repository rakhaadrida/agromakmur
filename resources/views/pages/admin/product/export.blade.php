<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Daftar Produk</h2>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Sub Kategori</th>
                    <th>Unit</th>
                    <th>Unit Konversi</th>
                    <th>Qty</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                    <tr class="text-dark">
                        <td>{{ ++$key }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category_name }}</td>
                        <td>{{ $product->subcategory_name }}</td>
                        <td>{{ $product->unit_name }}</td>
                        <td>{{ !empty($conversions[$product->id]) ? $conversions[$product->id]['unit_name'] : '' }}</td>
                        <td>{{ !empty($conversions[$product->id]) ? $conversions[$product->id]['quantity'] : '' }}</td>
                        <td class="text-center">{{ isActiveData($product) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
