<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Daftar Kategori</h2>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Batas Pengingat Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $key => $category)
                    <tr class="text-dark">
                        <td class="text-center">{{ ++$key }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->reminder_limit }}</td>
                        <td class="text-center">{{ isActiveData($category) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
