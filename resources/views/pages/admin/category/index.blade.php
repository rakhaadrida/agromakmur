@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Category List</h1>
            <div class="justify-content-end">
                <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>  Add New Category
                </a>
                <span class="vertical-hr mr-2 ml-1"></span>
                <a href="{{ route('categories.deleted') }}" class="btn btn-sm btn-outline-danger shadow-sm">
                    <i class="fas fa-trash-alt fa-sm text-dark-50 mr-1"></i>  Deleted Categories
                </a>
                <span class="vertical-hr mr-2 ml-1"></span>
                <a href="{{ route('categories.export') }}" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-file-excel fa-sm text-dark-50 mr-1"></i>  Download Excel
                </a>
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
                                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-fw fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-toggle="modal" data-target="#deleteCategoryModal" data-id="{{ $category->id }}">
                                            <i class="fas fa-fw fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-bold h4 p-2">No Data Available</td>
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
                <div class="modal-body text-dark">Are you sure you want to delete this data?</div>
                <div class="modal-footer">
                    <form action="" method="POST" id="deleteForm">
                        @csrf
                        @method('delete')
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
            const url = `{{ route('categories.destroy', '') }}` + '/' + categoryId;

            $('#deleteForm').attr('action', url);
        });
    </script>
@endpush
