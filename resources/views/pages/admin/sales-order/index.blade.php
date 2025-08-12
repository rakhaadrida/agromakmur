@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Daily Sales Order</h1>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card show">
                        <div class="card-body">
                            <form action="{{ route('sales-orders.index') }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row justify-content-center">
                                        <label for="startDate" class="col-auto col-form-label text-bold">Order Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" required>
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-bold ">up to</label>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}">
                                        </div>
                                        <div class="col-1 mt-1 main-transaction-button">
                                            <button type="submit" class="btn btn-primary btn-sm btn-block text-bold">Search</button>
                                        </div>
                                        <div class="col-1 mt-1 ml-n3">
                                            <button type="button" class="btn btn-success btn-sm btn-block text-bold">Export</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-number-transaction-index">No</th>
                                            <th class="align-middle th-code-transaction-index">Number</th>
                                            <th class="align-middle th-code-transaction-index">Date</th>
                                            <th class="align-middle th-name-transaction-index">Customer</th>
                                            <th class="align-middle th-tempo-transaction-index">Tempo</th>
                                            <th class="align-middle th-tempo-transaction-index">Invoice Age</th>
                                            <th class="align-middle th-total-transaction-index">Grand Total</th>
                                            <th class="align-middle th-status-transaction-index">Status</th>
                                            <th class="align-middle th-status-transaction-index">Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($salesOrders as $key => $salesOrder)
                                            <tr class="text-dark">
                                                <td class="align-middle text-center">{{ ++$key }}</td>
                                                <td class="align-middle">
                                                    <a href="{{ route('sales-orders.detail', $salesOrder->id) }}" class="btn btn-sm btn-link text-bold">
                                                        {{ $salesOrder->number }}
                                                    </a>
                                                </td>
                                                <td class="text-center align-middle" data-sort="{{ formatDate($salesOrder->date, 'Ymd') }}">{{ formatDate($salesOrder->date, 'd-M-y')  }}</td>
                                                <td class="align-middle">{{ $salesOrder->customer_name }}</td>
                                                <td class="text-center align-middle">{{ $salesOrder->tempo }} Day(s)</td>
                                                <td class="text-center align-middle">{{ getInvoiceAge($salesOrder->date, $salesOrder->tempo) }} Day(s)</td>
                                                <td class="text-right align-middle">{{ formatCurrency($salesOrder->grand_total) }}</td>
                                                <td class="text-center align-middle">{{ getSalesOrderStatusLabel($salesOrder->status) }}</td>
                                                <td class="text-center align-middle">{{ $salesOrder->user_name }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-bold text-dark h4 py-2">No Data Available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $.fn.datepicker.dates['id'] = {
            days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            daysShort: ['Mgu', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            daysMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            today: 'Hari Ini',
            clear: 'Kosongkan'
        };

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'id',
        });

        let datatable = $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    </script>
@endpush
