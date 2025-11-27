@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <h1 class="h3 mb-0 text-gray-800 menu-title">Daftar Unit yang Dihapus</h1>
            </div>
            <div class="row">
                <form action="{{ route('units.restore', 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm mr-2" title="Restore All Units">
                        Kembalikan Semua Unit
                    </button>
                </form>
                <a href="#" class="btn btn-sm btn-outline-danger shadow-sm mr-2 btn-delete" data-toggle="modal" data-target="#deleteUnitModal" data-id="0">
                    Hapus Permanen Semua Unit
                </a>
                <a href="{{ route('units.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">Kembali ke Daftar Unit</a>
            </div>
        </div>
        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                        <thead class="text-center text-bold text-dark">
                            <tr>
                                <th class="table-head-number">No</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($units as $key => $unit)
                                <tr class="text-dark">
                                    <td class="align-middle text-center">{{ ++$key }}</td>
                                    <td class="align-middle table-row-text">{{ $unit->name }}</td>
                                    <td class="align-middle text-center">
                                        <div class="row justify-content-center deleted-action-section">
                                            <div class="col-auto button-action-deleted-left">
                                                <form action="{{ route('units.restore', $unit->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Restore Unit">
                                                        <i class="fas fa-fw fa-undo"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <a href="#" class="btn btn-sm btn-danger btn-delete" data-toggle="modal" data-target="#deleteUnitModal" data-id="{{ $unit->id }}">
                                                <i class="fas fa-fw fa-eraser"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak Ada Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteUnitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-bold" id="exampleModalLabel">Konfirmasi</h5>
                </div>
                <div class="modal-body text-dark">Apakah Anda yakin ingin menghapus data ini secara permanen?</div>
                <div class="modal-footer">
                    <form action="" method="POST" id="deleteForm">
                        @csrf
                        @method('PUT')
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
            const unitId = $(this).data('id');
            const url = `{{ route('units.remove', 'unitId') }}`;

            $('#deleteForm').attr('action', url.replace('unitId', unitId));
        });
    </script>
@endpush
