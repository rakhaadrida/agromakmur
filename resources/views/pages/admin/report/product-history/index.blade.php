@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Laporan Histori Produk</h1>
        </div>
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card show">
                        <div class="card-body">
                            <div class="container">
                                <form>
                                    <div class="row justify-content-center mb-2">
                                        <h4 class="text-dark text-bold">Tanggal Laporan : {{ $reportDate }}</h4>
                                    </div>
                                    <div class="row justify-content-end product-history-export-button">
                                        <div class="col-2 product-history-col">
                                            <button type="submit" formaction="{{ route('report.product-histories.export') }}" formmethod="GET" class="btn btn-success btn-block text-bold">Export Excel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                <thead class="text-center text-bold text-dark">
                                    <tr class="bg-light">
                                        <th class="align-middle th-product-history-number">No</th>
                                        <th class="align-middle th-product-history-product-sku">SKU</th>
                                        <th class="align-middle">Nama Produk</th>
                                        <th class="align-middle th-product-history-branch">Cabang Terakhir</th>
                                        <th class="align-middle th-product-history-supplier">Supplier Terakhir</th>
                                        <th class="align-middle th-product-history-date">Tanggal BM Terakhir</th>
                                        <th class="align-middle th-product-history-latest-number">Nomor BM Terakhir</th>
                                        <th class="align-middle th-product-history-price">Harga Terakhir</th>
                                        <th class="align-middle th-product-history-quantity">Qty Terakhir</th>
                                        <th class="align-middle th-product-history-unit">Unit Terakhir</th>
                                        <th class="align-middle th-product-history-total">Total Terakhir</th>
                                        <th class="align-middle th-product-history-action">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $index => $product)
                                        <tr class="text-dark text-bold">
                                            <td class="align-middle text-center">{{ $index + 1 }}</td>
                                            <td class="align-middle text-center">{{ $product->product_sku }}</td>
                                            <td class="align-middle">{{ $product->product_name }}</td>
                                            <td class="align-middle">{{ $product->latest_branch }}</td>
                                            <td class="align-middle">{{ $product->latest_supplier }}</td>
                                            <td class="align-middle text-center" data-sort="{{ formatDate($product->latest_date, 'Ymd') }}">{{ formatDate($product->latest_date, 'd-m-Y') }}</td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('goods-receipts.detail', $product->latest_id) }}" class="btn btn-sm btn-link text-bold">
                                                    {{ $product->latest_number }}
                                                </a>
                                            </td>
                                            <td class="align-middle text-right">{{ formatPrice($product->latest_price) }}</td>
                                            <td class="align-middle text-right">{{ formatQuantity($product->latest_quantity) }}</td>
                                            <td class="align-middle text-center">{{ $product->latest_unit }}</td>
                                            <td class="align-middle text-right">{{ formatPrice($product->latest_price * $product->latest_quantity) }}</td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('report.product-histories.show', $product->product_id) }}" class="btn btn-sm btn-info text-bold">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center text-bold h4 p-2">Tidak Ada Data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
    <script type="text/javascript">
        let datatable = $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "pageLength": 25,
            "order": [
                [2, 'asc']
            ],
            "columnDefs": [
                {
                    targets: [0, 1, 3, 4, 6, 7, 8, 9, 10, 11],
                    orderable: false
                }
            ],
        });
    </script>
@endpush
