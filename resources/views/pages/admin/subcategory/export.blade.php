<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Daftar Sub Kategori</h2>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Batas Pengingat Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subcategories as $key => $subcategory)
                    <tr class="text-dark">
                        <td>{{ ++$key }}</td>
                        <td>{{ $subcategory->name }}</td>
                        <td>{{ $subcategory->category_name }}</td>
                        <td>{{ $subcategory->reminder_limit }}</td>
                        <td class="text-center">{{ isActiveData($subcategory) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
