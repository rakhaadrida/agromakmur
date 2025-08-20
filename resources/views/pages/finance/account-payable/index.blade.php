@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Account Payable</h1>
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
                            <form action="" method="">
                                @csrf
                                <div class="container so-container">
                                    <div class="form-group row" style="margin-top: -10px">
                                        <label for="month" class="col-2 col-form-label text-right text-bold">Month</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" tabindex="1" class="form-control form-control-sm text-bold mt-1" name="month" id="month" autofocus>
                                        </div>
                                        <label for="status" class="col-auto col-form-label text-right text-bold">Status</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <select class="form-control form-control-sm mt-1" tabindex="2" name="status" id="status">
                                                <option value="ALL" selected>ALL</option>
                                                <option value="PAID">PAID</option>
                                                <option value="UNPAID">UNPAID</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-top: -10px">
                                        <label for="startDate" class="col-2 col-form-label text-right text-bold">Start Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" tabindex="3" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate">
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-bold ml-3"> up to </label>
                                        <div class="col-2">
                                            <input type="text" tabindex="4" class="form-control datepicker form-control-sm text-bold mt-1 ml-1" name="final_date" id="finalDate">
                                        </div>
                                        <div class="col-1 mt-1" style="margin-left: -10px">
                                            <button type="submit" tabindex="5" formaction="" formmethod="GET" id="btnSearch" class="btn btn-primary btn-sm btn-block text-bold">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                <thead class="text-center text-bold text-dark">
                                    <tr>
                                        <th style="width: 30px" class="align-middle">No</th>
                                        <th style="width: 300px" class="align-middle">Supplier</th>
                                        <th style="width: 50px" class="align-middle">Invoice Count</th>
                                        <th style="width: 75px" class="align-middle">Grand Total</th>
                                        <th style="width: 70px" class="align-middle">Payment</th>
                                        <th style="width: 70px" class="align-middle">Return Amount</th>
                                        <th style="width: 70px" class="align-middle">Outstanding Amount</th>
                                        <th style="width: 60px" class="align-middle">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="table-ar">
                                    @forelse($accountPayables as $key => $accountPayable)
                                        <tr class="text-dark">
                                            <td class="align-middle text-center">{{ ++$key }}</td>
                                            <td class="align-middle"></td>
                                            <td class="align-middle text-center"></td>
                                            <td class="align-middle text-right">
                                                0
                                            </td>
                                            <td class="align-middle text-right">
                                                0
                                            </td>
                                            <td class="align-middle text-right">
                                                0
                                            </td>
                                            <td class="align-middle text-right">
                                                0
                                            </td>
                                            <td class="align-middle text-center text-bold">
                                                ALL
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-bold text-dark h4 py-2">No Data Available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="text-right text-bold text-dark" style="background-color: lightgrey; font-size: 14px">
                                        <td colspan="3" class="text-center">Total</td>
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
            "footerCallback": function (row, data, start, end, display) {
                let api = this.api();
                let intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                let column;
                $.each([3, 4, 5, 6], function(index, value) {
                    if((value === 3) || (value === 4) || (value === 5)) {
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
