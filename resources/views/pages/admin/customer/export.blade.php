<html lang="en">
    <body>
        <div class="justify-content-center">
            <h2 class="text-bold text-dark">Daftar Customer</h2>
            <h5>Tanggal Export : {{ $exportDate }}</h5>
        </div>
        <br>
        <table class="table table-sm table-bordered">
            <thead class="text-center text-dark text-bold">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>NPWP</th>
                    <th>Limit Kredit</th>
                    <th>Tempo</th>
                    <th>Sales</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $key => $customer)
                    <tr class="text-dark">
                        <td>{{ ++$key }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->address }}</td>
                        <td>{{ $customer->contact_number }}</td>
                        <td>{{ $customer->tax_number }}</td>
                        <td>{{ $customer->credit_limit }}</td>
                        <td>{{ $customer->tempo }}</td>
                        <td>{{ $customer->marketing_name }}</td>
                        <td class="text-center">{{ isActiveData($customer) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <h4>Copyright &copy; 2020 - {{ \Carbon\Carbon::now()->format('Y') }}  | {{ env('APP_DEVELOPER') }}</h4>
    </body>
</html>
