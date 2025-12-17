@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Barang Masuk Harian</h1>
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
                            <form action="{{ route('goods-receipts.index') }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row justify-content-center">
                                        <label for="startDate" class="col-auto col-form-label text-bold">Tanggal BM</label>
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
                                            <button type="submit" class="btn btn-success btn-sm btn-block text-bold" formaction="{{ route('goods-receipts.export') }}" formmethod="GET">Export</button>
                                        </div>
                                        <div class="col-1 mt-1 ml-n3">
                                            <button type="submit" class="btn btn-danger btn-sm btn-block text-bold" formaction="{{ route('goods-receipts.pdf') }}" formmethod="GET" formtarget="_blank">PDF</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-number-transaction-index">No</th>
                                            <th class="align-middle th-goods-receipt-number-index">Nomor</th>
                                            <th class="align-middle th-goods-receipt-date-index">Tanggal</th>
                                            <th class="align-middle th-goods-receipt-branch-index">Cabang</th>
                                            <th class="align-middle">Supplier</th>
                                            <th class="align-middle th-goods-receipt-invoice-age-index">Umur Nota</th>
                                            <th class="align-middle th-goods-receipt-grand-total-index">Grand Total</th>
                                            <th class="align-middle th-goods-receipt-status-index">Status</th>
                                            <th class="align-middle th-goods-receipt-status-index">Admin</th>
                                            <th class="align-middle th-goods-receipt-action-index">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($goodsReceipts as $key => $goodsReceipt)
                                            <tr class="text-dark">
                                                <td class="align-middle text-center">{{ ++$key }}</td>
                                                <td class="align-middle">
                                                    <a href="{{ route('goods-receipts.detail', $goodsReceipt->id) }}" class="btn btn-sm btn-link text-bold">
                                                        {{ $goodsReceipt->number }}
                                                    </a>
                                                </td>
                                                <td class="text-center align-middle" data-sort="{{ formatDate($goodsReceipt->date, 'Ymd') }}">{{ formatDateIso($goodsReceipt->date, 'DD-MMM-YY')  }}</td>
                                                <td class="align-middle">{{ $goodsReceipt->branch_name }}</td>
                                                <td class="align-middle">{{ $goodsReceipt->supplier_name }}</td>
                                                <td class="text-center align-middle" data-sort="{{ getInvoiceAge($goodsReceipt->date, $goodsReceipt->tempo) }}">{{ getInvoiceAge($goodsReceipt->date, $goodsReceipt->tempo) }} Hari</td>
                                                <td class="text-right align-middle" data-sort="{{ $goodsReceipt->grand_total }}">{{ formatPrice($goodsReceipt->grand_total) }}</td>
                                                <td class="text-center align-middle">{{ getGoodsReceiptStatusLabel($goodsReceipt->status) }}</td>
                                                <td class="text-center align-middle">{{ $goodsReceipt->user_name }}</td>
                                                <td class="align-middle text-center">
                                                    @if(!isCancelled($goodsReceipt->status))
                                                        <button type="submit" class="btn btn-sm btn-info text-bold edit-receipt" formaction="{{ route('goods-receipts.edit', $goodsReceipt->id) }}" formmethod="GET" id="btnEdit-{{ $key }}" data-index="{{ $key }}">Ubah</button>
                                                        <button type="button" class="btn btn-sm btn-danger text-bold cancel-receipt" id="btnCancel-{{ $key }}" data-toggle="modal" data-target="#modalCancelReceipt" data-id="{{ $goodsReceipt->id }}" data-number="{{ $goodsReceipt->number }}">Batal</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </form>

                            <div class="modal" id="modalCancelReceipt" tabindex="-1" role="dialog" aria-labelledby="modalCancelOrder" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" class="h2 text-bold">&times;</span>
                                            </button>
                                            <h4 class="modal-title">Batalkan Barang Masuk - <span id="modalOrderNumber"></span></h4>
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
                    targets: [0, 3, 7, 8, 9],
                    orderable: false
                }
            ],
        });

        $(document).ready(function() {
            const form = $('#form');
            const modalCancelReceipt = $('#modalCancelReceipt');

            form.on('click', '.edit-receipt', function (e) {
                if(!$(this).attr('data-validated')) {
                    e.preventDefault();

                    let subjectIndex = $(this).data('index');
                    $('#subjectIndex').val(subjectIndex);
                    $('#modalPasswordEdit').modal('show');
                } else {
                    $(this).removeAttr('data-validated');
                }
            });

            form.on('click', '.cancel-receipt', function () {
                const receiptId = $(this).data('id');
                const receiptNumber = $(this).data('number');
                const url = `{{ route('goods-receipts.destroy', '') }}` + '/' + receiptId;

                $('#modalOrderNumber').text(receiptNumber);
                $('#deleteForm').attr('action', url);
            });

            modalCancelReceipt.on('show.bs.modal', function (e) {
                $('#description').attr('required', true);
            })

            modalCancelReceipt.on('hide.bs.modal', function (e) {
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
