@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Notification List</h1>
            <div class="justify-content-end">
                <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline-block">
                    @csrf
                    <button class="btn btn-sm btn-success shadow-sm">Mark All As Read</button>
                </form>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                        <thead class="text-center text-bold text-dark">
                            <tr>
                                <th class="align-middle th-number-transaction-index">No</th>
                                <th class="align-middle th-date-notification-index">Date</th>
                                <th class="align-middle">Message</th>
                                <th class="align-middle th-action-notification-index">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($notifications as $key => $notification)
                                <tr class="text-dark">
                                    <td class="align-middle text-center">{{ ++$key }}</td>
                                    <td class="align-middle text-center">{{ formatDate($notification->created_at, 'd M y h:i:s') }} </td>
                                    <td class="align-middle">{{ $notification->data['message'] }}</td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('notifications.show', ['notification' => $notification->data['approval_id'], 'notification_id' => $notification->id]) }}" class="btn btn-sm btn-info d-inline-block">See Details</a>
                                        <form action="{{ route('notifications.update', $notification->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-sm btn-success">Mark As Read</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-bold text-dark h4 py-2">No Data Available</td>
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
    <script src="{{ url('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        let datatable = $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    </script>
@endpush
