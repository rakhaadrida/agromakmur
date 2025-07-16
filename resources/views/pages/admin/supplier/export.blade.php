<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Supplier Data</h2>
            <h5>Export Date : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Tax Number</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $key => $supplier)
                    <tr class="text-dark">
                        <td>{{ ++$key }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->address }}</td>
                        <td>{{ $supplier->contact_number }}</td>
                        <td>{{ $supplier->tax_number }}</td>
                        <td class="text-center">{{ isActiveData($supplier) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
