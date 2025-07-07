@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-2">
      <h1 class="h3 mb-0 text-gray-800 menu-title">Data User Tidak Terpakai</h1>
      <a href="{{ route('user.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">
      Kembali ke Data User</a>
  </div>

  <div class="row">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable" width="100%" cellspacing="0">
          <a href="{{ route('user-restoreAll') }}" class="btn btn-sm btn-primary shadow-sm mb-3">Kembalikan Semua Data</a>
          <a href="{{ route('user-hapusAll') }}" class="btn btn-sm btn-outline-danger shadow-sm mb-3 ml-2">Hapus Permanen Semua Data</a>
          <thead class="text-center text-bold text-dark">
            <tr align="center">
              <th style="width: 80px">No</th>
              <th>Username</th>
              <th>Roles</th>
              <th style="width: 160px">Kembalikan</th>
              <th style="width: 200px">Hapus Permanen</th>
            </tr>
          </thead>
          <tbody>
            @php $i=1; @endphp
            @forelse ($items as $item)
              <tr class="text-dark">
                <td class="align-middle" align="center">{{ $i }}</td>
                <td class="align-middle">{{ $item->name }}</td>
                <td class="align-middle" align="center">{{ $item->roles }}</td>
                <td class="align-middle" align="center">
                  <a href="{{ route('user-restore', $item->id) }}" class="btn btn-success btn-sm"><i class="fas fa-fw fa-undo"></i></a>
                </td>
                <td class="align-middle" align="center">
                  <a href="{{ route('user-hapus', $item->id) }}" class="btn btn-danger btn-sm"><i class="fas fa-fw fa-eraser"></i></a>
                </td>
              </tr>
              @php $i++; @endphp
            @empty
              <tr>
                <td colspan="5" class="text-center">Tidak Ada Data</td>
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
    <script src="{{ url('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('backend/js/demo/datatables-demo.js') }}"></script>
@endpush
