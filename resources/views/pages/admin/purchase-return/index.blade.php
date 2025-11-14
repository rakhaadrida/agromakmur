@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Purchase Return List</h1>
            <div class="justify-content-end">
                <a href="{{ route('purchase-returns.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>  Add New Purchase Return
                </a>
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
                    <div class="card show">
                        <div class="card-body">
                            <form action="{{ route('purchase-returns.index') }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row account-payable-filter-row">
                                        <label for="status" class="col-2 col-form-label text-right text-bold">Status</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 account-payable-filter-status">
                                            <select class="form-control form-control-sm mt-1" name="status" id="status" tabindex="2">
                                                <option value="0" selected>All</option>
                                                @foreach($purchaseReturnStatuses as $purchaseReturnStatus)
                                                    <option value="{{ $purchaseReturnStatus }}" @if($status == $purchaseReturnStatus) selected @endif>{{ getPurchaseReturnStatusLabel($purchaseReturnStatus) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="receiptStatus" class="col-auto col-form-label text-right text-bold">Receipt Status</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 account-payable-filter-status">
                                            <select class="form-control form-control-sm mt-1" name="receipt_status" id="receiptStatus" tabindex="2">
                                                <option value="0" selected>All</option>
                                                @foreach($purchaseReturnReceiptStatuses as $purchaseReturnReceiptStatus)
                                                    <option value="{{ $purchaseReturnReceiptStatus }}" @if($receiptStatus == $purchaseReturnReceiptStatus) selected @endif>{{ getPurchaseReturnReceiptStatusLabel($purchaseReturnReceiptStatus) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row account-payable-filter-row">
                                        <label for="startDate" class="col-2 col-form-label text-right text-bold">Start Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" tabindex="3">
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-right text-bold ml-1 purchase-return-final-date">Final Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}" tabindex="4">
                                        </div>
                                        <div class="col-1 mt-1 ml-n2">
                                            <button type="submit" id="btnSubmit" class="btn btn-success btn-sm btn-block text-bold">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-sales-return-number">No</th>
                                            <th class="align-middle th-sales-return-return-number">Return Number</th>
                                            <th class="align-middle th-sales-return-date">Return Date</th>
                                            <th class="align-middle th-sales-return-branch">Branch</th>
                                            <th class="align-middle">Supplier</th>
                                            <th class="align-middle th-sales-return-order-number">Receipt Number</th>
                                            <th class="align-middle th-sales-return-quantity">Qty</th>
                                            <th class="align-middle th-sales-return-quantity">Qty Sent</th>
                                            <th class="align-middle th-sales-return-quantity">Cut Bills</th>
                                            <th class="align-middle th-sales-return-quantity">Qty Left</th>
                                            <th class="align-middle th-sales-return-status">Status</th>
                                            <th class="align-middle th-sales-return-status">Receipt Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-ar">
                                        @forelse($purchaseReturns as $key => $purchaseReturn)
                                            <tr class="text-dark">
                                                <td class="align-middle text-center">{{ ++$key }}</td>
                                                <td class="align-middle text-center">{{ $purchaseReturn->number }}</td>
                                                <td class="align-middle text-center" data-sort="{{ formatDate($purchaseReturn->date, 'Ymd') }}">{{ formatDate($purchaseReturn->date, 'd-M-y') }}</td>
                                                <td class="align-middle">{{ $purchaseReturn->branch_name }}</td>
                                                <td class="align-middle">{{ $purchaseReturn->supplier_name }}</td>
                                                <td class="align-middle">
                                                    <a href="{{ route('goods-receipts.detail', $purchaseReturn->goods_receipt_id) }}" class="btn btn-link btn-sm text-bold tbody-payable-status">{{ $purchaseReturn->goods_receipt_number }}</a>
                                                </td>
                                                <td class="align-middle text-right" data-sort="{{ $purchaseReturn->quantity }}">{{ formatQuantity($purchaseReturn->quantity) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $purchaseReturn->received_quantity }}">{{ formatPrice($purchaseReturn->received_quantity) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $purchaseReturn->cut_bill_quantity }}">{{ formatPrice($purchaseReturn->cut_bill_quantity) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $purchaseReturn->remaining_quantity }}">{{ formatPrice($purchaseReturn->remaining_quantity) }}</td>
                                                <td class="align-middle text-center">{{ getPurchaseReturnStatusLabel($purchaseReturn->status) }}</td>
                                                <td class="align-middle text-center text-bold @if(isPurchaseReturnActive($purchaseReturn->receipt_status)) account-payable-unpaid @elseif(isPurchaseReturnOngoing($purchaseReturn->receipt_status)) account-payable-ongoing @else account-payable-paid @endif">
                                                    <a href="{{ route('purchase-returns.edit', $purchaseReturn->id) }}" class="btn btn-link btn-sm text-bold tbody-payable-status">{{ getPurchaseReturnReceiptStatusLabel($purchaseReturn->receipt_status) }}</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center text-bold text-dark h4 py-2">No Data Available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="text-right text-bold text-dark tfoot-account-payable">
                                            <td colspan="6" class="text-center">Total</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>
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
    <script type="text/javascript">
        $.fn.datepicker.dates['id'] = {
            days:["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"],
            daysShort:["Mgu","Sen","Sel","Rab","Kam","Jum","Sab"],
            daysMin:["Min","Sen","Sel","Rab","Kam","Jum","Sab"],
            months:["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"],
            monthsShort:["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"],
            today:"Hari Ini",
            clear:"Kosongkan"
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
                    targets: [0, 3, 5, 10, 11],
                    orderable: false
                }
            ],
            "footerCallback": function (row, data, start, end, display) {
                let api = this.api();

                let intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$.]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                let column;
                $.each([6, 7, 8, 9], function(index, value) {
                    if((value === 6) || (value === 7) || (value === 8) || (value === 9)) {
                        column = api
                            .column(value, {
                                page: 'current'
                            })
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                    }
                    else {
                        column = api
                            .column(value, {
                                page: 'current'
                            })
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal($(b).val());
                            }, 0 );
                    }

                    $(api.column(value).footer()).html(thousandSeparator(column));
                });
            }
        });

        function thousandSeparator(nStr) {
            nStr += '';
            x = nStr.split(',');
            x1 = x[0];
            x2 = x.length > 1 ? ',' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + '.' + '$2');
            }
            return x1 + x2;
        }
    </script>
@endpush
