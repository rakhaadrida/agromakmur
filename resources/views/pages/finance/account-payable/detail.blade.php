@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Account Payable - {{ $supplier->name }}</h1>
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
                            <form action="{{ route('account-payables.detail', $id) }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row account-payable-filter-row">
                                        <label for="status" class="col-2 col-form-label text-right text-bold">Status</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-5 account-payable-filter-status">
                                            <select class="form-control form-control-sm mt-1" name="status" id="status" tabindex="2">
                                                <option value="0" selected>All</option>
                                                @foreach($accountPayableStatuses as $accountPayableStatus)
                                                    <option value="{{ $accountPayableStatus }}" @if($status == $accountPayableStatus) selected @endif>{{ getAccountPayableStatusLabel($accountPayableStatus) }}</option>
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
                                        <label for="finalDate" class="col-auto col-form-label text-bold ml-1"> up to </label>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1 ml-1" name="final_date" id="finalDate" value="{{ $finalDate }}" tabindex="4">
                                        </div>
                                        <div class="col-1 mt-1 ml-n2">
                                            <button type="submit" id="btnSubmit" class="btn btn-primary btn-sm btn-block text-bold">Search</button>
                                        </div>
                                        <div class="col-1 mt-1 ml-n3">
                                            <button type="submit" class="btn btn-success btn-sm btn-block text-bold" formaction="{{ route('account-payables.export-detail', $id) }}" formmethod="GET">Export</button>
                                        </div>
                                        <div class="col-1 mt-1 ml-n3">
                                            <button type="submit" class="btn btn-danger btn-sm btn-block text-bold" formaction="{{ route('account-payables.pdf-detail', $id) }}" formmethod="GET" formtarget="_blank">PDF</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-payable-number">No</th>
                                            <th class="align-middle">Receipt Number</th>
                                            <th class="align-middle th-payable-date">Receipt Date</th>
                                            <th class="align-middle th-payable-date">Due Date</th>
                                            <th class="align-middle th-payable-invoice-age">Invoice Age</th>
                                            <th class="align-middle th-payable-branch">Branch</th>
                                            <th class="align-middle th-payable-grand-total">Grand Total</th>
                                            <th class="align-middle th-payable-amount">Payment</th>
                                            <th class="align-middle th-payable-amount">Return Amount</th>
                                            <th class="align-middle th-payable-amount">Outstanding Amount</th>
                                            <th class="align-middle th-payable-status">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-ar">
                                        @forelse($accountPayables as $key => $accountPayable)
                                            <tr class="text-dark">
                                                <td class="align-middle text-center">{{ ++$key }}</td>
                                                <td class="align-middle">
                                                    <a href="{{ route('goods-receipts.detail', $accountPayable->goods_receipt_id) }}" class="btn btn-link btn-sm text-bold tbody-payable-status">{{ $accountPayable->number }}</a>
                                                </td>
                                                <td class="align-middle text-center" data-sort="{{ formatDate($accountPayable->date, 'Ymd') }}">{{ formatDate($accountPayable->date, 'd-m-Y') }}</td>
                                                <td class="align-middle text-center" data-sort="{{ getDueDate($accountPayable->date, $accountPayable->tempo, 'Ymd') }}">{{ getDueDate($accountPayable->date, $accountPayable->tempo, 'd-m-Y') }}</td>
                                                <td class="align-middle text-center">{{ getInvoiceAge($accountPayable->date, $accountPayable->tempo) }} Day(s)</td>
                                                <td class="align-middle">{{ $accountPayable->branch_name }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $accountPayable->grand_total }}">{{ formatPrice($accountPayable->grand_total) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $accountPayable->payment_amount }}">{{ formatPrice($accountPayable->payment_amount) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $accountPayable->return_amount }}">
                                                    <input type="hidden" value="{{ $accountPayable->return_amount }}">
                                                    <a href="{{ route('account-payables.return', $accountPayable->id) }}" class="text-bold tbody-payable-status">{{ formatPrice($accountPayable->return_amount) }}</a>
                                                </td>
                                                <td class="align-middle text-right" data-sort="{{ $accountPayable->outstanding_amount }}">{{ formatPrice($accountPayable->outstanding_amount) }}</td>
                                                <td class="align-middle text-center text-bold @if(isAccountPayableUnpaid($accountPayable->status)) account-payable-unpaid @elseif(isAccountPayableOngoing($accountPayable->status)) account-payable-ongoing @else account-payable-paid @endif">
                                                    <a href="{{ route('account-payables.payment', $accountPayable->id) }}" class="btn btn-link btn-sm text-bold tbody-payable-status">{{ getAccountPayableStatusLabel($accountPayable->status) }}</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center text-bold text-dark h4 py-2">No Data Available</td>
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
                                            <td></td>
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
                    targets: [0, 4, 5, 10],
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
                    if((value === 6) || (value === 7) || (value === 9)) {
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
