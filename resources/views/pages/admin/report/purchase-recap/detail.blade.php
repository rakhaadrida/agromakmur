@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Rekap Pembelian - {{ $item->name }}</h1>
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
                            <form action="{{ route('report.purchase-recap.show', $item->id) }}" method="GET" id="form">
                                <div class="container so-container">
                                    @if(isSubjectProduct($subject))
                                        <div class="form-group row">
                                            <label for="supplier" class="col-2 col-form-label text-bold text-right filter-supplier-receipt">Supplier</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-5">
                                                <select class="selectpicker product-history-select-picker" name="supplier_id" id="supplier" data-live-search="true" data-size="6" title="Pilih Supplier">
                                                    @foreach($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" data-tokens="{{ $supplier->name }}" @if($supplierId == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group row">
                                            <label for="product" class="col-2 col-form-label text-bold text-right filter-supplier-receipt">Produk</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-5">
                                                <select class="selectpicker product-history-select-picker" name="product_id" id="product" data-live-search="true" data-size="6" title="Pilih Produk">
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-tokens="{{ $product->name }}" @if($productId == $product->id) selected @endif>{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group row filter-date-marketing-report">
                                        <label for="startDate" class="col-2 col-form-label text-bold text-right">Tanggal Awal</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" tabindex="3">
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-bold text-right filter-final-date-receipt">Tanggal Akhir</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}" tabindex="4">
                                        </div>
                                        <input type="hidden" name="subject" value="{{ $subject }}">
                                        <div class="col-1 mt-1 btn-search-receipt">
                                            <button type="submit" id="btnSearch" class="btn btn-primary btn-sm btn-block text-bold" tabindex="5">Cari</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="container" style="margin-bottom: 0">
                                    <div class="row justify-content-center">
                                        <h4 class="text-bold text-dark">Rekap {{ $item->name }} ({{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}) </h4>
                                    </div>
                                    <div class="row justify-content-center mb-2">
                                        <h6 class="text-dark">Tanggal Laporan : {{ $reportDate }}</h6>
                                    </div>
                                    <div class="row justify-content-end product-history-detail-export-button">
                                        <div class="col-2 product-history-col">
                                            <input type="hidden" name="subject" value="{{ $subject }}">
                                            <button type="submit" formaction="{{ route('report.purchase-recap.export-detail', $item->id) }}" formmethod="GET" class="btn btn-success btn-block text-bold">Export Excel</button>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    @if(isSubjectProduct($subject))
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="align-middle th-sales-recap-number">No</td>
                                                <td class="align-middle th-sales-recap-detail-date">Tanggal BM</td>
                                                <td class="align-middle th-purchase-recap-detail-number">Nomor BM</td>
                                                <td class="align-middle">Supplier</td>
                                                <td class="align-middle th-sales-recap-detail-quantity">Qty</td>
                                                <td class="align-middle th-sales-recap-detail-unit">Unit</td>
                                                <td class="align-middle th-purchase-recap-detail-price">Harga</td>
                                                <td class="align-middle th-purchase-recap-detail-price">Upah</td>
                                                <td class="align-middle th-purchase-recap-detail-price">Ongkos Kirim</td>
                                                <td class="align-middle th-purchase-recap-detail-price">Harga Modal</td>
                                                <td class="align-middle th-purchase-recap-detail-total">Total</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($purchaseItems as $index => $purchaseItem)
                                                <tr class="text-dark text-bold">
                                                    <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($purchaseItem->receipt_date, 'Ymd') }}">{{ formatDate($purchaseItem->receipt_date, 'd-m-Y') }}</td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('goods-receipts.detail', $purchaseItem->receipt_id) }}" class="btn btn-sm btn-link text-bold">
                                                            {{ $purchaseItem->receipt_number }}
                                                        </a>
                                                    </td>
                                                    <td class="align-middle">{{ $purchaseItem->supplier_name }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->quantity }}">{{ formatQuantity($purchaseItem->quantity) }}</td>
                                                    <td class="align-middle text-center">{{ $purchaseItem->unit_name }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->price }}">{{ formatPrice($purchaseItem->price) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->wages }}">{{ formatPrice($purchaseItem->wages) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->shipping_cost }}">{{ formatPrice($purchaseItem->shipping_cost) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->cost_price }}">{{ formatPrice($purchaseItem->cost_price) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->total }}">{{ formatPrice($purchaseItem->total) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-dark text-bold h4 p-2">Tidak Ada Data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    @else
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="align-middle th-sales-recap-number">No</td>
                                                <td class="align-middle th-sales-recap-detail-date">Tanggal BM</td>
                                                <td class="align-middle th-purchase-recap-detail-number">Nomor BM</td>
                                                <td class="align-middle">Product Name</td>
                                                <td class="align-middle th-sales-recap-detail-quantity">Qty</td>
                                                <td class="align-middle th-sales-recap-detail-unit">Unit</td>
                                                <td class="align-middle th-purchase-recap-detail-price">Harga</td>
                                                <td class="align-middle th-purchase-recap-detail-price">Upah</td>
                                                <td class="align-middle th-purchase-recap-detail-price">Ongkos Kirim</td>
                                                <td class="align-middle th-purchase-recap-detail-price">Harga Modal</td>
                                                <td class="align-middle th-purchase-recap-detail-total">Total</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($purchaseItems as $index => $purchaseItem)
                                                <tr class="text-dark text-bold">
                                                    <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($purchaseItem->receipt_date, 'Ymd') }}">{{ formatDate($purchaseItem->receipt_date, 'd-m-Y') }}</td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('goods-receipts.detail', $purchaseItem->receipt_id) }}" class="btn btn-sm btn-link text-bold">
                                                            {{ $purchaseItem->receipt_number }}
                                                        </a>
                                                    </td>
                                                    <td class="align-middle">{{ $purchaseItem->product_name }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->quantity }}">{{ formatQuantity($purchaseItem->quantity) }}</td>
                                                    <td class="align-middle text-center">{{ $purchaseItem->unit_name }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->price }}">{{ formatPrice($purchaseItem->price) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->wages }}">{{ formatPrice($purchaseItem->wages) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->shipping_cost }}">{{ formatPrice($purchaseItem->shipping_cost) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->cost_price }}">{{ formatPrice($purchaseItem->cost_price) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $purchaseItem->total }}">{{ formatPrice($purchaseItem->total) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-dark text-bold h4 p-2">Tidak Ada Data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    @endif
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
                    targets: [0, 2, 3, 5],
                    orderable: false
                }
            ],
        });
    </script>
@endpush
