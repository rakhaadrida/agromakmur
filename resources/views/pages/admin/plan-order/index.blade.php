@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Plan Order Harian</h1>
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
                            <form action="{{ route('plan-orders.index') }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row justify-content-center">
                                        <label for="startDate" class="col-auto col-form-label text-bold">Tanggal PO</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" required>
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-bold ">s / d</label>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}">
                                        </div>
                                        <div class="col-1 mt-1 main-transaction-button">
                                            <button type="submit" class="btn btn-primary btn-sm btn-block text-bold">Cari</button>
                                        </div>
                                        <div class="col-1 mt-1 ml-n3">
                                            <button type="submit" class="btn btn-success btn-sm btn-block text-bold" formaction="{{ route('plan-orders.export') }}" formmethod="GET">Export</button>
                                        </div>
                                        <div class="col-1 mt-1 ml-n3">
                                            <button type="submit" class="btn btn-danger btn-sm btn-block text-bold" formaction="{{ route('plan-orders.pdf') }}" formmethod="GET" formtarget="_blank">PDF</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-number-transaction-index">No</th>
                                            <th class="align-middle th-plan-order-number-index">Nomor PO</th>
                                            <th class="align-middle th-plan-order-date-index">Tanggal</th>
                                            <th class="align-middle th-plan-order-branch-index">Cabang</th>
                                            <th class="align-middle">Supplier</th>
                                            <th class="align-middle th-plan-order-total-items-index">Total Barang</th>
                                            <th class="align-middle th-goods-receipt-status-index">Status</th>
                                            <th class="align-middle th-goods-receipt-status-index">Admin</th>
                                            <th class="align-middle th-goods-receipt-action-index">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($planOrders as $key => $planOrder)
                                            <tr class="text-dark">
                                                <td class="align-middle text-center">{{ ++$key }}</td>
                                                <td class="align-middle">
                                                    <a href="{{ route('plan-orders.detail', $planOrder->id) }}" class="btn btn-sm btn-link text-bold">
                                                        {{ $planOrder->number }}
                                                    </a>
                                                </td>
                                                <td class="text-center align-middle" data-sort="{{ formatDate($planOrder->date, 'Ymd') }}">{{ formatDateIso($planOrder->date, 'DD-MMM-YY')  }}</td>
                                                <td class="align-middle">{{ $planOrder->branch_name }}</td>
                                                <td class="align-middle">{{ $planOrder->supplier_name }}</td>
                                                <td class="align-middle text-center" data-sort="{{ $planOrder->planOrderItems->count() }}">{{ formatQuantity($planOrder->planOrderItems->count()) }}</td>
                                                <td class="text-center align-middle">{{ getPlanOrderStatusLabel($planOrder->status) }}</td>
                                                <td class="text-center align-middle">{{ $planOrder->user_name }}</td>
                                                <td class="align-middle text-center">
                                                    @if(!isCancelled($planOrder->status))
                                                        <button type="submit" class="btn btn-sm btn-info text-bold edit-order" formaction="{{ route('plan-orders.edit', $planOrder->id) }}" formmethod="GET" id="btnEdit-{{ $key }}" data-index="{{ $key }}">Ubah</button>
                                                        <button type="button" class="btn btn-sm btn-danger text-bold cancel-order" id="btnCancel-{{ $key }}" data-toggle="modal" data-target="#modalCancelOrder" data-id="{{ $planOrder->id }}" data-number="{{ $planOrder->number }}">Batal</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </form>

                            <div class="modal" id="modalCancelOrder" tabindex="-1" role="dialog" aria-labelledby="modalCancelOrder" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" class="h2 text-bold">&times;</span>
                                            </button>
                                            <h4 class="modal-title">Batalkan Plan Order - <span id="modalOrderNumber"></span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST" id="deleteForm">
                                                @csrf
                                                @method('DELETE')
                                                <div class="form-group row">
                                                    <label for="status" class="col-2 col-form-label text-bold">Status</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-3">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="status" id="status" value="CANCEL" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group subtotal-so">
                                                    <label for="description" class="col-form-label">Deskripsi</label>
                                                    <input type="text" class="form-control" name="description" id="description">
                                                    <input type="hidden" class="form-control" name="start_date" value="{{ $startDate }}">
                                                    <input type="hidden" class="form-control" name="final_date" value="{{ $finalDate }}">
                                                </div>
                                                <hr>
                                                <div class="form-row justify-content-center">
                                                    <div class="col-3">
                                                        <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit">Simpan</button>
                                                    </div>
                                                    <div class="col-3">
                                                        <button type="button" class="btn btn-outline-secondary btn-block text-bold" data-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
            "order": [
                [2, "desc"]
            ],
            "columnDefs": [
                {
                    targets: [0, 6, 7, 8],
                    orderable: false
                }
            ],
        });

        $(document).ready(function() {
            const form = $('#form');
            const modalCancelOrder = $('#modalCancelOrder');

            form.on('click', '.edit-order', function (e) {
                if(!$(this).attr('data-validated')) {
                    e.preventDefault();

                    let subjectIndex = $(this).data('index');
                    $('#subjectIndex').val(subjectIndex);
                    $('#modalPasswordEdit').modal('show');
                } else {
                    $(this).removeAttr('data-validated');
                }
            });

            form.on('click', '.cancel-order', function () {
                const orderId = $(this).data('id');
                const orderNumber = $(this).data('number');
                const url = `{{ route('plan-orders.destroy', '') }}` + '/' + orderId;

                $('#modalOrderNumber').text(orderNumber);
                $('#deleteForm').attr('action', url);
            });

            modalCancelOrder.on('show.bs.modal', function (e) {
                $('#description').attr('required', true);
            })

            modalCancelOrder.on('hide.bs.modal', function (e) {
                $('#description').removeAttr('required');
            })
        });

        function submitForm(index) {
            const sourceButton = $('#btnEdit-' + index);
            sourceButton.attr('data-validated', 'true');
            sourceButton.trigger('click');
        }
    </script>
@endpush
