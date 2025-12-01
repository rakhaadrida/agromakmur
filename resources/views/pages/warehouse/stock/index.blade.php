@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Stok Produk</h1>
        </div>
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                        <thead class="text-center text-bold text-dark">
                            <tr>
                                <th class="align-middle th-product-number">No</th>
                                <th class="align-middle th-product-name">Nama</th>
                                <th class="align-middle th-product-category">Kategori</th>
                                @foreach($warehouses as $warehouse)
                                    <th class="align-middle th-product-stock">{{ $warehouse->name }}</th>
                                @endforeach
                                <th class="align-middle th-product-action">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $key => $product)
                                <tr class="text-dark">
                                    <td class="align-middle text-center">{{ ++$key }}</td>
                                    <td class="align-middle table-row-text">{{ $product->name }}</td>
                                    <td class="align-middle table-row-text">{{ $product->category_name }} - {{ $product->subcategory_name }}</td>
                                    @foreach($warehouses as $warehouse)
                                        <td class="align-middle text-right table-row-text">{{ formatQuantity($mapStockByProductWarehouse[$product->id][$warehouse->id] ?? 0) }}</td>
                                    @endforeach
                                    <td class="align-middle text-center">
                                        <a href="{{ route('stocks.show', $product->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-fw fa-eye"></i>
                                        </a>
                                    </td>
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
        let warehouses = @json($warehouses);

        let targetColumn = 3;
        if(warehouses.length) {
            targetColumn += warehouses.length;
        }

        let datatable = $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                {
                    targets: [targetColumn],
                    orderable: false
                }
            ],
        });
    </script>
@endpush
