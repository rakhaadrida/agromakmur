@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Sales Recap Detail - {{ $item->name }}</h1>
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
                            <form action="{{ route('report.sales-recap.show', $item->id) }}" method="GET" id="form">
                                <div class="container so-container">
                                    @if(isSubjectProduct($subject))
                                        <div class="form-group row">
                                            <label for="customer" class="col-2 col-form-label text-bold text-right filter-supplier-receipt">Customer</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-5">
                                                <select class="selectpicker product-history-select-picker" name="customer_id" id="customer" data-live-search="true" data-size="6" title="Choose Customer">
                                                    @foreach($customers as $customer)
                                                        <option value="{{ $customer->id }}" data-tokens="{{ $customer->name }}" @if($customerId == $customer->id) selected @endif>{{ $customer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group row">
                                            <label for="product" class="col-2 col-form-label text-bold text-right filter-supplier-receipt">Product</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-5">
                                                <select class="selectpicker product-history-select-picker" name="product_id" id="product" data-live-search="true" data-size="6" title="Choose Product">
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-tokens="{{ $product->name }}" @if($productId == $product->id) selected @endif>{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
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
                                        <input type="hidden" name="subject" value="{{ $subject }}">
                                        <div class="col-1 mt-1 btn-search-receipt">
                                            <button type="submit" id="btnSearch" class="btn btn-primary btn-sm btn-block text-bold" tabindex="5">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="container" style="margin-bottom: 0">
                                    <div class="row justify-content-center">
                                        <h4 class="text-bold text-dark">Recap {{ $item->name }} ({{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}) </h4>
                                    </div>
                                    <div class="row justify-content-center mb-2">
                                        <h6 class="text-dark">Report Time : {{ $reportDate }}</h6>
                                    </div>
                                    <div class="row justify-content-end product-history-detail-export-button">
                                        <div class="col-2 product-history-col">
                                            <input type="hidden" name="subject" value="{{ $subject }}">
                                            <button type="submit" formaction="{{ route('report.sales-recap.export-detail', $item->id) }}" formmethod="GET" class="btn btn-success btn-block text-bold">Export Excel</button>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    @if(isSubjectProduct($subject))
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="align-middle th-sales-recap-number">No</td>
                                                <td class="align-middle th-sales-recap-detail-date">Order Date</td>
                                                <td class="align-middle th-sales-recap-detail-number">Order Number</td>
                                                <td class="align-middle">Customer</td>
                                                <td class="align-middle th-sales-recap-detail-quantity">Qty</td>
                                                <td class="align-middle th-sales-recap-detail-unit">Unit</td>
                                                <td class="align-middle th-sales-recap-detail-price">Price</td>
                                                <td class="align-middle th-sales-recap-detail-total">Total</td>
                                                <td class="align-middle th-sales-recap-detail-discount">Discount (%)</td>
                                                <td class="align-middle th-sales-recap-detail-discount-amount">Discount Amount</td>
                                                <td class="align-middle th-sales-recap-detail-final-amount">Final Amount</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($salesItems as $index => $salesItem)
                                                <tr class="text-dark text-bold">
                                                    <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($salesItem->order_date, 'Ymd') }}">{{ formatDate($salesItem->order_date, 'd-m-Y') }}</td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('sales-orders.detail', $salesItem->order_id) }}" class="btn btn-sm btn-link text-bold">
                                                            {{ $salesItem->order_number }}
                                                        </a>
                                                    </td>
                                                    <td class="align-middle">{{ $salesItem->customer_name }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->quantity }}">{{ formatQuantity($salesItem->quantity) }}</td>
                                                    <td class="align-middle text-center">{{ $salesItem->unit_name }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->price }}">{{ formatPrice($salesItem->price) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->total }}">{{ formatPrice($salesItem->total) }}</td>
                                                    <td class="align-middle text-right">{{ $salesItem->discount }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->discount_amount }}">{{ formatPrice($salesItem->discount_amount) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->final_amount }}">{{ formatPrice($salesItem->final_amount) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center text-dark text-bold h4 p-2">No Data Available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    @else
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="align-middle th-sales-recap-number">No</td>
                                                <td class="align-middle th-sales-recap-detail-date">Order Date</td>
                                                <td class="align-middle th-sales-recap-detail-number">Order Number</td>
                                                <td class="align-middle">Product Name</td>
                                                <td class="align-middle th-sales-recap-detail-quantity">Qty</td>
                                                <td class="align-middle th-sales-recap-detail-unit">Unit</td>
                                                <td class="align-middle th-sales-recap-detail-price">Price</td>
                                                <td class="align-middle th-sales-recap-detail-total">Total</td>
                                                <td class="align-middle th-sales-recap-detail-discount">Discount (%)</td>
                                                <td class="align-middle th-sales-recap-detail-discount-amount">Discount Amount</td>
                                                <td class="align-middle th-sales-recap-detail-final-amount">Final Amount</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($salesItems as $index => $salesItem)
                                                <tr class="text-dark text-bold">
                                                    <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($salesItem->order_date, 'Ymd') }}">{{ formatDate($salesItem->order_date, 'd-m-Y') }}</td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('sales-orders.detail', $salesItem->order_id) }}" class="btn btn-sm btn-link text-bold">
                                                            {{ $salesItem->order_number }}
                                                        </a>
                                                    </td>
                                                    <td class="align-middle">{{ $salesItem->product_name }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->quantity }}">{{ formatQuantity($salesItem->quantity) }}</td>
                                                    <td class="align-middle text-center">{{ $salesItem->unit_name }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->price }}">{{ formatPrice($salesItem->price) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->total }}">{{ formatPrice($salesItem->total) }}</td>
                                                    <td class="align-middle text-right">{{ $salesItem->discount }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->discount_amount }}">{{ formatPrice($salesItem->discount_amount) }}</td>
                                                    <td class="align-middle text-right" data-sort="{{ $salesItem->final_amount }}">{{ formatPrice($salesItem->final_amount) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center text-dark text-bold h4 p-2">No Data Available</td>
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
                    targets: [0, 2, 3, 5, 8],
                    orderable: false
                }
            ],
        });
    </script>
@endpush
