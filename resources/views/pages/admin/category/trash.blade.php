@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <h1 class="h3 mb-0 text-gray-800 menu-title">List of Deleted Categories</h1>
            </div>
            <div class="row">
                <form action="{{ route('categories.restore', 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm mr-2" title="Restore All Categories">
                        Restore All Categories
                    </button>
                </form>
                <a href="#" class="btn btn-sm btn-outline-danger shadow-sm mr-2 btn-delete" data-toggle="modal" data-target="#deleteCategoryModal" data-id="0">
                    Permanently Delete All Categories
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">Back to Categories List</a>
            </div>
        </div>
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                        <thead class="text-center text-bold text-dark">
                            <tr>
                                <th class="table-head-number">No</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $key => $category)
                                <tr class="text-dark">
                                    <td class="align-middle text-center">{{ ++$key }}</td>
                                    <td class="align-middle table-row-text">{{ $category->name }}</td>
                                    <td class="align-middle text-center">
                                        <div class="row justify-content-center deleted-action-section">
                                            <div class="col-auto button-action-deleted-left">
                                                <form action="{{ route('categories.restore', $category->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Restore Category">
                                                        <i class="fas fa-fw fa-undo"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <a href="#" class="btn btn-sm btn-danger btn-delete" data-toggle="modal" data-target="#deleteCategoryModal" data-id="{{ $category->id }}">
                                                <i class="fas fa-fw fa-eraser"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No Data Available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-bold" id="exampleModalLabel">Confirmation</h5>
                </div>
                <div class="modal-body text-dark">Are you sure you want to permanently delete this data?</div>
                <div class="modal-footer">
                    <form action="" method="POST" id="deleteForm">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-md btn-danger">Delete</button>
                        <button type="button" class="btn btn-md btn-outline-primary text-sm" data-dismiss="modal">Cancel</button>
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
        let datatable = $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                {
                    targets: [2],
                    orderable: false
                }
            ],
        });

        $(document).on('click', '.btn-delete', function () {
            const categoryId = $(this).data('id');
            const url = `{{ route('categories.remove', 'categoryId') }}`;

            $('#deleteForm').attr('action', url.replace('categoryId', categoryId));
        });
    </script>
@endpush
