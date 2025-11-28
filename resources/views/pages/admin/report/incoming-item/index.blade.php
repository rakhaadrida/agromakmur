@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Laporan Barang Masuk</h1>
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
                            <form action="{{ route('report.incoming-items.index') }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row justify-content-center">
                                        <label for="startDate" class="col-auto col-form-label text-bold">Tanggal Awal</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" required>
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-bold ">s / d</label>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}">
                                        </div>
                                        <div class="col-1 mt-1 main-transaction-button">
                                            <button type="submit" class="btn btn-primary btn-sm btn-block text-bold">Cari</button>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center" style="margin-bottom: 15px">
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                            <button type="submit" formaction="{{ route('report.incoming-items.export') }}" formmethod="GET" class="btn btn-danger btn-block text-bold">Export Excel</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="container" style="margin-bottom: 0">
                                    <div class="row justify-content-center">
                                        <h4 class="text-bold text-dark">Laporan Barang Masuk ({{ formatDateIso($startDate, 'D MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}) </h4>
                                    </div>
                                    <div class="row justify-content-center" style="margin-top: -5px">
                                        <h6 class="text-dark">Tanggal Laporan : {{ $reportDate }}</h6>
                                    </div>
                                </div>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-dark text-bold">
                                        <tr>
                                            <td class="align-middle th-incoming-items-number">No</td>
                                            <td class="align-middle th-incoming-items-supplier">Supplier</td>
                                            <td class="align-middle th-incoming-items-product-sku">SKU</td>
                                            <td class="align-middle">Nama Produk</td>
                                            <td class="align-middle th-incoming-items-warehouse">Gudang</td>
                                            <td class="align-middle th-incoming-items-total-quantity">Qty</td>
                                            <td class="align-middle th-incoming-items-unit">Unit</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($receiptItems as $index => $receiptItem)
                                            <tr class="text-dark text-bold">
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $receiptItem->supplier_name }}</td>
                                                <td class="text-center">{{ $receiptItem->product_sku }}</td>
                                                <td>{{ $receiptItem->product_name }}</td>
                                                <td class="text-center">{{ $receiptItem->warehouse_name }}</td>
                                                <td class="text-right" style="background-color: yellow" data-sort="{{ $receiptItem->total_quantity }}">{{ formatQuantity($receiptItem->total_quantity ?? 0) }}</td>
                                                <td class="text-center">{{ $receiptItem->unit_name }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-dark text-bold h4 p-2">Tidak Ada Data</td>
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
            "pageLength": 100,
        });
    </script>
@endpush
