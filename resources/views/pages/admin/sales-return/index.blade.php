@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Sales Return List</h1>
            <div class="justify-content-end">
                <a href="{{ route('sales-returns.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>  Add New Sales Return
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
                            <form action="{{ route('sales-returns.index') }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row account-payable-filter-row">
                                        <label for="status" class="col-2 col-form-label text-right text-bold">Status</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 account-payable-filter-status">
                                            <select class="form-control form-control-sm mt-1" name="status" id="status" tabindex="2">
                                                <option value="0" selected>All</option>
                                                @foreach($salesReturnStatuses as $salesReturnStatus)
                                                    <option value="{{ $salesReturnStatus }}" @if($status == $salesReturnStatus) selected @endif>{{ getSalesReturnStatusLabel($salesReturnStatus) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="deliveryStatus" class="col-auto col-form-label text-right text-bold">Delivery Status</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 account-payable-filter-status">
                                            <select class="form-control form-control-sm mt-1" name="delivery_status" id="deliveryStatus" tabindex="2">
                                                <option value="0" selected>All</option>
                                                @foreach($salesReturnDeliveryStatuses as $salesReturnDeliveryStatus)
                                                    <option value="{{ $salesReturnDeliveryStatus }}" @if($deliveryStatus == $salesReturnDeliveryStatus) selected @endif>{{ getSalesReturnDeliveryStatusLabel($salesReturnDeliveryStatus) }}</option>
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
                                        <label for="finalDate" class="col-auto col-form-label text-right text-bold ml-1 sales-return-final-date">Final Date</label>
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
                                            <th class="align-middle th-sales-return-customer">Customer</th>
                                            <th class="align-middle th-sales-return-order-number">Order Number</th>
                                            <th class="align-middle th-sales-return-quantity">Qty</th>
                                            <th class="align-middle th-sales-return-quantity">Delivered Qty</th>
                                            <th class="align-middle th-sales-return-quantity">Cut Bill Qty</th>
                                            <th class="align-middle th-sales-return-quantity">Remaining Qty</th>
                                            <th class="align-middle th-sales-return-status">Status</th>
                                            <th class="align-middle th-sales-return-status">Delivery Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-ar">
                                        @forelse($salesReturns as $key => $salesReturn)
                                            <tr class="text-dark">
                                                <td class="align-middle text-center">{{ ++$key }}</td>
                                                <td class="align-middle text-center">{{ $salesReturn->number }}</td>
                                                <td class="align-middle text-center" data-sort="{{ formatDate($salesReturn->date, 'Ymd') }}">{{ formatDate($salesReturn->date, 'd-M-Y') }}</td>
                                                <td class="align-middle">{{ $salesReturn->customer_name }}</td>
                                                <td class="align-middle">
                                                    <a href="{{ route('sales-orders.detail', $salesReturn->sales_order_id) }}" class="btn btn-link btn-sm text-bold tbody-payable-status">{{ $salesReturn->sales_order_number }}</a>
                                                </td>
                                                <td class="align-middle text-right" data-sort="{{ $salesReturn->quantity }}">{{ formatQuantity($salesReturn->quantity) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $salesReturn->delivered_quantity }}">{{ formatPrice($salesReturn->delivered_quantity) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $salesReturn->cut_bill_quantity }}">{{ formatPrice($salesReturn->cut_bill_quantity) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $salesReturn->remaining_quantity }}">{{ formatPrice($salesReturn->remaining_quantity) }}</td>
                                                <td class="align-middle text-center">{{ getSalesReturnStatusLabel($salesReturn->status) }}</td>
                                                <td class="align-middle text-center text-bold @if(isSalesReturnActive($salesReturn->delivery_status)) account-payable-unpaid @elseif(isSalesReturnOngoing($salesReturn->delivery_status)) account-payable-ongoing @else account-payable-paid @endif">
                                                    <a href="{{ route('sales-returns.edit', $salesReturn->id) }}" class="btn btn-link btn-sm text-bold tbody-payable-status">{{ getSalesReturnDeliveryStatusLabel($salesReturn->delivery_status) }}</a>
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
                                            <td colspan="5" class="text-center">Total</td>
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
            "columnDefs": [
                {
                    targets: [9, 10],
                    orderable: false
                }
            ],
            "drawCallback": function(settings) {
                var api = this.api();
                api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            },
            "footerCallback": function (row, data, start, end, display) {
                let api = this.api();

                let intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$.]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                let column;
                $.each([5, 6, 7, 8], function(index, value) {
                    if((value === 5) || (value === 6) || (value === 7) || (value === 8)) {
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
