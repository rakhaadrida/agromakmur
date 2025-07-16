<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Category Data</h2>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $key => $category)
                    <tr class="text-dark">
                        <td class="text-center">{{ ++$key }}</td>
                        <td>{{ $category->name }}</td>
                        <td class="text-center">{{ isActiveData($category) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
