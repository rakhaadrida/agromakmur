@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Kartu Stok</h1>
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
                            <form action="{{ route('report.stock-cards.index') }}" method="GET">
                                <div class="container so-container">
                                    <div class="form-group row">
                                        <label for="productSKU" class="col-2 col-form-label text-bold text-right filter-supplier-receipt">SKU Produk</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <select class="selectpicker stock-card-sku-select-picker" name="product_id" id="productSku" data-live-search="true" data-size="6" title="Pilih SKU">
                                                @foreach($products as $item)
                                                    <option value="{{ $item->id }}" data-tokens="{{ $item->sku }}" @if($productId == $item->id) selected @endif>{{ $item->sku }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="productName" class="col-auto col-form-label text-bold text-right filter-product-name-stock-card">Nama Produk</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4">
                                            <select class="selectpicker product-history-select-picker" name="product_name" id="productName" data-live-search="true" data-size="6" title="Pilih Nama Produk">
                                                @foreach($products as $item)
                                                    <option value="{{ $item->id }}" data-tokens="{{ $item->name }}" @if($productId == $item->id) selected @endif>{{ $item->name }}</option>
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
                                        <label for="finalDate" class="col-auto col-form-label text-bold text-right filter-final-date-stock-card">Tanggal Akhir</label>
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
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <h5 class="text-bold text-dark">
                                            Nama Produk : {{ $product->name ?? '' }}
                                        </h5>
                                    </div>
                                    <div class="row justify-content-center stock-card-report-date">
                                        <h5 class="text-bold text-dark">Tanggal Laporan : {{ formatDateIso($startDate, 'D MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h5>
                                    </div>
                                    <div class="row justify-content-end stock-card-export-button">
                                        <div class="col-2">
                                            <button type="submit" formaction="{{ route('report.stock-cards.export') }}" formmethod="GET" class="btn btn-success btn-block text-bold">Export Excel</button>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover table-stock-card">
                                    <thead class="text-center text-bold text-dark th-stock-card-background">
                                        <tr>
                                            <td rowspan="2" class="align-middle th-stock-card-number">No</td>
                                            <td rowspan="2" class="align-middle th-stock-card-date">Tanggal</td>
                                            <td rowspan="2" class="align-middle th-stock-card-transaction-number">Nomor Transaksi</td>
                                            <td rowspan="2" class="align-middle th-stock-card-type">Tipe</td>
                                            <td rowspan="2" class="align-middle">Customer / Supplier</td>
                                            <td colspan="3" class="align-middle">Masuk</td>
                                            <td colspan="3" class="align-middle">Keluar</td>
                                            <td rowspan="2" class="align-middle th-stock-card-user">Admin</td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle th-stock-card-quantity">{{ $product ? $product->unit->name : 'Unit' }}</td>
                                            <td class="align-middle th-stock-card-warehouse">Gudang</td>
                                            <td class="align-middle th-stock-card-amount">Jumlah</td>
                                            <td class="align-middle th-stock-card-quantity">{{ $product ? $product->unit->name : 'Unit' }}</td>
                                            <td class="align-middle th-stock-card-warehouse">Gudang</td>
                                            <td class="align-middle th-stock-card-amount">Jumlah</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($stockLogs->count() > 0)
                                            <tr>
                                                <td colspan="5" class="text-bold text-dark text-center">Stok Awal</td>
                                                <td class="text-bold text-dark text-right">{{ formatQuantity($initialStock) }}</td>
                                                <td colspan="6"></td>
                                            </tr>
                                            @foreach($stockLogs as $index => $stockLog)
                                                <tr class="text-dark">
                                                    <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                    <td class="align-middle text-center">{{ formatDateIso(!isManualLog($stockLog->type) ? $stockLog->subject->date : $stockLog->subject_date, 'D-MMM-YY') }}</td>
                                                    @if(isTransactionLog($stockLog->type))
                                                        <td class="align-middle text-center">
                                                            <a href="{{ route(''. getProductStockLogTypeRoute($stockLog->type) .'.detail', $stockLog->subject_id) }}" class="btn btn-sm btn-link text-bold">
                                                                {{ $stockLog->subject->number }}
                                                            </a>
                                                        </td>
                                                    @elseif(isReturnLog($stockLog->type))
                                                        <td class="align-middle text-center">
                                                            <a href="{{ route(''. getProductStockLogTypeRoute($stockLog->type) .'.show', $stockLog->subject_id) }}" class="btn btn-sm btn-link text-bold">
                                                                {{ $stockLog->subject->number }}
                                                            </a>
                                                        </td>
                                                    @else
                                                        <td class="align-middle text-center">
                                                            <a href="{{ route('products.edit', $stockLog->subject_id) }}" class="btn btn-sm btn-link text-bold">
                                                                {{ !isManualLog($stockLog->type) ? $stockLog->subject->number : '' }}
                                                            </a>
                                                        </td>
                                                    @endif
                                                    <td class="align-middle">{{ getProductStockLogTypeLabel($stockLog->type) }}</td>
                                                    <td class="align-middle">
                                                        @if(isSupplierLog($stockLog->type)) {{ $stockLog->supplier_name }} @elseif(isCustomerLog($stockLog->type)) {{ $stockLog->customer_name }} @else - @endif
                                                    </td>
                                                    <td class="align-middle text-right">{{ $stockLog->quantity >= 0 ? formatQuantity($stockLog->quantity) : '' }}</td>
                                                    <td class="align-middle">{{ $stockLog->quantity >= 0 ? $stockLog->warehouse->name : '' }}</td>
                                                    <td class="align-middle text-right">{{ $stockLog->quantity >= 0 ? formatPrice($stockLog->final_amount) : '' }}</td>
                                                    <td class="align-middle text-right">{{ $stockLog->quantity < 0 ? formatQuantity($stockLog->quantity * -1) : '' }}</td>
                                                    <td class="align-middle">{{ $stockLog->quantity < 0 ? $stockLog->warehouse_name : '' }}</td>
                                                    <td class="align-middle text-right">{{ $stockLog->quantity < 0 ? formatPrice($stockLog->final_amount) : '' }}</td>
                                                    <td class="align-middle text-center">{{ $stockLog->user->username }} {{ formatDate($stockLog->subject->created_at, 'H:i:s') }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5" class="text-bold text-dark text-center">Total</td>
                                                <td class="text-bold text-dark text-right">{{ formatQuantity($initialStock + $totalIncomingQuantity) }}</td>
                                                <td colspan="2"></td>
                                                <td class="text-bold text-dark text-right">{{ formatQuantity($totalOutgoingQuantity * -1) }}</td>
                                                <td colspan="3"></td>
                                            </tr>
                                            <tr class="th-stock-card-background">
                                                <td colspan="5" class="text-bold text-dark text-center">Stok Akhir</td>
                                                <td class="text-bold text-dark text-right">{{ formatQuantity($initialStock + $totalIncomingQuantity - ($totalOutgoingQuantity * -1)) }}</td>
                                                <td colspan="6"></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="12" class="text-center text-bold text-dark h4 p-2">Tidak Ada Data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <hr>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $.fn.datepicker.dates['id'] = {
            days:["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"],
            daysShort:["Mgu","Sen","Sel","Rab","Kam","Jum","Sab"],
            daysMin:["Min","Sen","Sel","Rab","Kam","Jum","Sab"],
            months:["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"],
            monthsShort:["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"],
            today:"Hari Ini",
            clear:"Kosongkan"
        };

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'id',
        });

        $(document).ready(function() {
            $('#productSku').change(function() {
                let productId = $(this).val();

                $('#productName').selectpicker('val', productId);
            });

            $('#productName').change(function() {
                let productId = $(this).val();

                $('#productSku').selectpicker('val', productId);
            });
        });
    </script>
@endpush
