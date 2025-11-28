@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Laporan Stok Rendah</h1>
        </div>
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card show">
                        <div class="card-body">
                            <div class="container">
                                <div class="row justify-content-center mb-2">
                                    <h4 class="text-dark text-bold">Tanggal Laporan : {{ $reportDate }}</h4>
                                </div>
                            </div>
                            <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                <thead class="text-center text-bold text-dark">
                                    <tr class="bg-light">
                                        <th class="align-middle th-low-stock-number">No</th>
                                        <th class="align-middle th-low-stock-product-sku">SKU</th>
                                        <th class="align-middle">Nama Produk</th>
                                        <th class="align-middle th-low-stock-category">Kategori</th>
                                        <th class="align-middle th-low-stock-unit">Unit</th>
                                        <th class="align-middle th-low-stock-amount">Stok Saat Ini</th>
                                        <th class="align-middle th-low-stock-limit">Limit Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $index => $product)
                                        <tr class="text-dark text-bold">
                                            <td class="align-middle text-center">{{ $index + 1 }}</td>
                                            <td class="align-middle text-center">{{ $product->product_sku }}</td>
                                            <td class="align-middle">{{ $product->product_name }}</td>
                                            <td class="align-middle">{{ $product->category_name }} - {{ $product->subcategory_name }}</td>
                                            <td class="align-middle text-center">{{ $product->unit_name }}</td>
                                            <td class="align-middle text-center td-low-stock-amount" data-sort="{{ $product->current_stock }}">
                                                <a href="{{ route('products.stock', $product->id) }}" class="btn btn-sm btn-link text-bold a-low-stock-amount">
                                                    {{ formatQuantity($product->current_stock) }}
                                                </a>
                                            </td>
                                            <td class="align-middle text-right" data-sort="{{ $product->stock_limit }}">{{ formatQuantity($product->stock_limit) }}</td>
                                        </tr>
                                    @endforeach
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
        $(document).ready(function() {
            let datatable = $('#dataTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "pageLength": 25,
                "order": [
                    [5, 'asc']
                ],
                "columnDefs": [
                    {
                        targets: [0, 3, 4],
                        orderable: false
                    }
                ],
                "language": {
                    "emptyTable": `<span class="text-center text-bold text-dark h4 py-3">No Data Available</span>`
                },
            });
        });
    </script>
@endpush
