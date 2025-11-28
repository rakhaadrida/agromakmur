@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Daftar Stok Retur</h1>
        </div>
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                        <thead class="text-center text-bold text-dark">
                            <tr>
                                <th class="align-middle th-return-number">No</th>
                                <th class="align-middle th-return-product-sku">SKU</th>
                                <th class="align-middle th-return-product-name">Nama Produk</th>
                                <th class="align-middle th-return-stock">Stok</th>
                                <th class="align-middle th-return-unit-name">Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $key => $product)
                                <tr class="text-dark">
                                    <td class="align-middle text-center">{{ ++$key }}</td>
                                    <td class="align-middle">{{ $product->sku }}</td>
                                    <td class="align-middle">{{ $product->name }}</td>
                                    <td class="align-middle text-center" data-sort="{{ $product->stock }}">{{ formatQuantity($product->stock) }}</td>
                                    <td class="align-middle text-center">{{ $product->unit->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-bold h4 p-2">Tidak Ada Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
            "columnDefs": [
                {
                    targets: [4],
                    orderable: false
                }
            ],
        });
    </script>
@endpush
