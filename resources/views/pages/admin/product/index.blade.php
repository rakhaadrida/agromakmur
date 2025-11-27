@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Daftar Produk</h1>
            <div class="justify-content-end">
                <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>  Tambah Produk Baru
                </a>
                <span class="vertical-hr mr-2 ml-1"></span>
                <a href="{{ route('products.deleted') }}" class="btn btn-sm btn-outline-danger shadow-sm">
                    <i class="fas fa-trash-alt fa-sm text-dark-50 mr-1"></i>  Produk yang Dihapus
                </a>
                <span class="vertical-hr mr-2 ml-1"></span>
                <a href="{{ route('products.export') }}" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-file-excel fa-sm text-dark-50 mr-1"></i>  Export Excel
                </a>
            </div>
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
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-fw fa-edit"></i>
                                        </a>
                                        <a href="{{ route('products.stock', $product->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-fw fa-warehouse"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-toggle="modal" data-target="#deleteProductModal" data-id="{{ $product->id }}">
                                            <i class="fas fa-fw fa-trash"></i>
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

    <div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-bold" id="exampleModalLabel">Konfirmasi</h5>
                </div>
                <div class="modal-body text-dark">Apakah Anda yakin ingin menghapus data ini secara permanen?</div>
                <div class="modal-footer">
                    <form action="" method="POST" id="deleteForm">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-md btn-danger">Hapus</button>
                        <button type="button" class="btn btn-md btn-outline-primary text-sm" data-dismiss="modal">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script type="text/javascript">
        let warehouseCount = {{ $warehouses->count() }};
        let unorderedColumn = 3 + warehouseCount;

        let datatable = $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                {
                    targets: [unorderedColumn],
                    orderable: false
                }
            ],
        });

        $(document).on('click', '.btn-delete', function () {
            const productId = $(this).data('id');
            const url = `{{ route('products.destroy', '') }}` + '/' + productId;

            $('#deleteForm').attr('action', url);
        });
    </script>
@endpush
