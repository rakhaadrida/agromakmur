@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Rekap Qty Sales</h1>
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
                            <form action="{{ route('report.marketing-recap.index') }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row">
                                        <label for="marketing" class="col-2 col-form-label text-bold text-right">Sales</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <select class="selectpicker marketing-report-select-picker" name="marketing_id" id="marketing" data-live-search="true" data-size="6" title="Pilih Sales" tabindex="2">
                                                @foreach($marketings as $marketing)
                                                    <option value="{{ $marketing->id }}" data-tokens="{{ $marketing->name }}" @if($marketingId == $marketing->id) selected @endif>{{ $marketing->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="category" class="col-auto col-form-label text-bold text-right filter-supplier-receipt">Kategori</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <select class="selectpicker marketing-report-select-picker" name="category_id" id="category" data-live-search="true" data-size="6" title="Pilih Kategori" tabindex="2">
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" data-tokens="{{ $category->name }}" @if($categoryId == $category->id) selected @endif>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
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
                                        <div class="col-1 mt-1 btn-search-receipt">
                                            <button type="submit" id="btnSearch" class="btn btn-primary btn-sm btn-block text-bold" tabindex="5">Cari</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="container" style="margin-bottom: 0">
                                    <div class="row justify-content-center">
                                        <h4 class="text-bold text-dark">Rekap Qty Sales ({{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}) </h4>
                                    </div>
                                    <div class="row justify-content-center" style="margin-top: -5px">
                                        <h6 class="text-dark">Tanggal Laporan : {{ $reportDate }}</h6>
                                    </div>
                                </div>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                    <thead class="text-center text-dark text-bold">
                                        <tr>
                                            <td class="align-middle th-marketing-recap-number">No</td>
                                            <td class="align-middle th-marketing-recap-customer">Customer</td>
                                            <td class="align-middle th-marketing-recap-product-sku">SKU</td>
                                            <td class="align-middle">Nama Produk</td>
                                            <td class="align-middle th-marketing-recap-category">Kategori</td>
                                            <td class="align-middle th-marketing-recap-total-quantity">Qty</td>
                                            <td class="align-middle th-marketing-recap-unit">Unit</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($marketingItems as $marketing)
                                            <tr class="text-dark text-bold" style="background-color: rgb(255, 221, 181)">
                                                <td colspan="7" class="text-center">
                                                    <button type="button" class="btn btn-link btn-sm text-dark text-bold" data-toggle="collapse" data-target="#collapseMarketing-{{ $marketing->id }}" aria-expanded="false" aria-controls="collapseMarketing-{{ $marketing->id }}" style="padding: 0; font-size: 15px; width: 100%">{{ $marketing->name }}</button>
                                                </td>
                                            </tr>
                                            @forelse($mapSalesOrderByMarketing[$marketing->id] ?? [] as $index => $item)
                                                <tr class="text-dark text-bold collapse show" id="collapseMarketing-{{ $marketing->id }}">
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $item->customer_name }}</td>
                                                    <td class="text-center">{{ $item->product_sku }}</td>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td class="text-center">{{ $item->category_name }}</td>
                                                    <td class="text-right" style="background-color: yellow" data-sort="{{ $item->total_quantity }}">{{ formatQuantity($item->total_quantity ?? 0) }}</td>
                                                    <td class="text-center">{{ $item->unit_name }}</td>
                                                </tr>
                                            @empty
                                                <tr class="collapse show" id="collapseMarketing-{{ $marketing->id }}">
                                                    <td colspan="7" class="text-center text-dark text-bold h4 p-2">Tidak Ada Data</td>
                                                </tr>
                                            @endforelse
                                            @if(isNotEmptyMarketingRecap($mapSalesOrderByMarketing[$marketing->id] ?? []))
                                                <tr class="text-white text-bold bg-primary collapse show" id="collapseMarketing-{{ $marketing->id }}">
                                                    <td class="text-right" colspan="5">Total Qty Sales</td>
                                                    <td class="text-right">{{ formatQuantity($mapTotalQuantityByMarketing[$marketing->id] ?? 0) }}</td>
                                                    <td></td>
                                                </tr>
                                            @endif
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
    </script>
@endpush
