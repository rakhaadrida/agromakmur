@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Product History Detail - {{ $product->name }}</h1>
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
                            <form action="{{ route('report.product-histories.show', $product->id) }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row">
                                        <label for="supplier" class="col-2 col-form-label text-bold text-right filter-supplier-receipt">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-5">
                                            <select class="selectpicker product-history-select-picker" name="supplier_id" id="supplier" data-live-search="true" data-size="6" title="Choose Supplier">
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}" data-tokens="{{ $supplier->name }}" @if($supplierId == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row filter-date-marketing-report">
                                        <label for="startDate" class="col-2 col-form-label text-bold text-right">Start Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" tabindex="3">
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-bold text-right filter-final-date-receipt">Final Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}" tabindex="4">
                                        </div>
                                        <div class="col-1 mt-1 btn-search-receipt">
                                            <button type="submit" id="btnSearch" class="btn btn-primary btn-sm btn-block text-bold" tabindex="5">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <h4 class="text-bold text-dark">History {{ $product->name }} ({{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}) </h4>
                                    </div>
                                    <div class="row justify-content-center mb-2">
                                        <h6 class="text-dark">Report Time : {{ $reportDate }}</h6>
                                    </div>
                                    <div class="row justify-content-end product-history-detail-export-button">
                                        <div class="col-2 product-history-col">
                                            <button type="submit" formaction="{{ route('report.product-histories.export-detail', $product->id) }}" formmethod="GET" class="btn btn-success btn-block text-bold">Export Excel</button>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-dark text-bold">
                                        <tr class="bg-light">
                                            <td class="align-middle th-marketing-recap-number">No</td>
                                            <th class="align-middle th-product-history-date">Receipt Date</th>
                                            <th class="align-middle th-product-history-latest-number">Receipt Number</th>
                                            <th class="align-middle th-product-history-detail-supplier">Supplier</th>
                                            <th class="align-middle th-product-history-detail-price">Price</th>
                                            <th class="align-middle th-product-history-detail-quantity">Qty</th>
                                            <th class="align-middle th-product-history-detail-unit">Unit</th>
                                            <th class="align-middle th-product-history-detail-price">Wages</th>
                                            <th class="align-middle th-product-history-detail-price">Shipping Cost</th>
                                            <th class="align-middle th-product-history-detail-total">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($receiptItems as $index => $receiptItem)
                                            <tr class="text-dark text-bold">
                                                <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                <td class="align-middle text-center" data-sort="{{ formatDate($receiptItem->receipt_date, 'Ymd') }}">{{ formatDate($receiptItem->receipt_date, 'd-m-Y') }}</td>
                                                <td class="align-middle text-center">
                                                    <a href="{{ route('goods-receipts.detail', $receiptItem->receipt_id) }}" class="btn btn-sm btn-link text-bold">
                                                        {{ $receiptItem->receipt_number }}
                                                    </a>
                                                </td>
                                                <td class="align-middle">{{ $receiptItem->supplier_name }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $receiptItem->price }}">{{ formatPrice($receiptItem->price) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $receiptItem->quantity }}">{{ formatQuantity($receiptItem->quantity) }}</td>
                                                <td class="align-middle text-center">{{ $receiptItem->unit_name }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $receiptItem->wages }}">{{ formatPrice($receiptItem->wages) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $receiptItem->shipping_cost }}">{{ formatPrice($receiptItem->shipping_cost) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $receiptItem->total }}">{{ formatPrice($receiptItem->total) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-dark text-bold h4 p-2">No Data Available</td>
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
            "pageLength": 25,
            "order": [
                [1, 'desc']
            ],
            "columnDefs": [
                {
                    targets: [0, 2, 3, 6],
                    orderable: false
                }
            ],
        });
    </script>
@endpush
