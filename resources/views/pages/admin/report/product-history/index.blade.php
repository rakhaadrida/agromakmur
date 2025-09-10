@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Product History Report</h1>
        </div>
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card show">
                        <div class="card-body">
                            <div class="container">
                                <div class="row justify-content-center mb-2">
                                    <h4 class="text-dark text-bold">Report Time : {{ $reportDate }}</h4>
                                </div>
                            </div>
                            <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                <thead class="text-center text-bold text-dark">
                                    <tr class="bg-light">
                                        <th class="align-middle th-product-history-number">No</th>
                                        <th class="align-middle th-product-history-product-sku">Product SKU</th>
                                        <th class="align-middle">Product Name</th>
                                        <th class="align-middle th-product-history-supplier">Latest Supplier</th>
                                        <th class="align-middle th-product-history-date">Latest Receipt Date</th>
                                        <th class="align-middle th-product-history-latest-number">Latest Receipt Number</th>
                                        <th class="align-middle th-product-history-price">Latest Price</th>
                                        <th class="align-middle th-product-history-quantity">Latest Qty</th>
                                        <th class="align-middle th-product-history-unit">Latest Unit</th>
                                        <th class="align-middle th-product-history-total">Latest Total</th>
                                        <th class="align-middle th-product-history-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $index => $product)
                                        <tr class="text-dark text-bold">
                                            <td class="align-middle text-center">{{ $index + 1 }}</td>
                                            <td class="align-middle text-center">{{ $product->product_sku }}</td>
                                            <td class="align-middle">{{ $product->product_name }}</td>
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
                                            <td colspan="10" class="text-center text-bold h4 p-2">No Data Available</td>
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
                    targets: [0, 1, 3, 5, 6, 7, 8, 9, 10],
                    orderable: false
                }
            ],
        });
    </script>
@endpush
