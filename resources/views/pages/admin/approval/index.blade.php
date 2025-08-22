@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Approval List</h1>
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
                    <div class="card show">
                        <div class="card-body">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pillsSalesOrderTab" data-bs-toggle="pill" data-bs-target="#pillsSalesOrder" type="button" role="tab" aria-controls="pills-sales-order" aria-selected="true">Sales Order</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pillsGoodsReceiptTab" data-bs-toggle="pill" data-bs-target="#pillsGoodsReceipt" type="button" role="tab" aria-controls="pills-goods-receipt" aria-selected="false">Goods Receipt</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pillsDeliveryOrderTab" data-bs-toggle="pill" data-bs-target="#pillsDeliveryOrder" type="button" role="tab" aria-controls="pills-delivery-order" aria-selected="false">Delivery Order</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pillsSalesOrder" role="tabpanel" aria-labelledby="pillsSalesOrderTab">
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <th class="align-middle th-number-transaction-index">No</th>
                                                <th class="align-middle th-code-transaction-index">Order Number</th>
                                                <th class="align-middle th-code-transaction-index">Order Date</th>
                                                <th class="align-middle th-status-transaction-index">Request Date</th>
                                                <th class="align-middle th-name-transaction-index">Customer</th>
                                                <th class="align-middle th-status-transaction-index">Status</th>
                                                <th class="align-middle th-warehouse-transaction-index">Description</th>
                                                <th class="align-middle th-code-transaction-index">Admin</th>
                                                <th class="align-middle th-code-transaction-index">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($approvals as $key => $approval)
                                                <tr class="text-dark">
                                                    <td class="align-middle text-center">{{ ++$key }}</td>
                                                    <td class="align-middle">
                                                        <a href="{{ route('sales-orders.detail', $approval->subject_id) }}" class="btn btn-sm btn-link text-bold">
                                                            {{ $approval->subject->number }}
                                                        </a>
                                                    </td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($approval->subject->date, 'Ymd') }}">{{ formatDate($approval->subject->date, 'd-M-y')  }}</td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($approval->date, 'Ymd') }}">{{ formatDate($approval->date, 'd-M-y')  }}</td>
                                                    <td class="align-middle">{{ $approval->customer_name }}</td>
                                                    <td class="align-middle text-center">{{ getApprovalStatusLabel($approval->status) }}</td>
                                                    <td class="align-middle">{{ getApprovalStatusLabel($approval->status) }}</td>
                                                    <td class="align-middle text-center">{{ $approval->description }} Day(s)</td>
                                                    <td class="align-middle text-center">{{ $approval->user_name }}</td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('approvals.edit', $approval->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-fw fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center text-bold text-dark h4 py-2">No Data Available</td>
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
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $.fn.datepicker.dates['id'] = {
            days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            daysShort: ['Mgu', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            daysMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            today: 'Hari Ini',
            clear: 'Kosongkan'
        };

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'id',
        });

        let datatable = $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    </script>
@endpush
