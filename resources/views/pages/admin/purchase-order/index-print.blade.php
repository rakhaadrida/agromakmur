@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Print Purchase Order</h1>
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
                            <form action="" id="form">
                                @csrf
                                <div class="container so-container">
                                    <div class="form-group row justify-content-center">
                                        <label for="startNumber" class="col-auto col-form-label text-bold">PO Number</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <select class="selectpicker supplier-select-picker" name="start_number" id="startNumber" data-live-search="true" title="Enter Start Number" required>
                                                @foreach($purchaseOrders as $purchaseOrder)
                                                    <option value="{{ $purchaseOrder->id }}" data-tokens="{{ $purchaseOrder->number }}">{{ $purchaseOrder->number }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="finalNumber" class="col-auto col-form-label text-bold ">up to</label>
                                        <div class="col-2">
                                            <input type="text" tabindex="2" class="form-control form-control-sm mt-1" name="finalNumber" id="finalNumber" placeholder="Final Number" required>
                                        </div>
                                        <div class="col-2 mt-1" style="margin-left: -10px">
                                            <button type="submit" tabindex="3" id="btnPrint" class="btn btn-success btn-sm btn-block text-bold">Print</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="text-center text-bold text-dark">
                                        <th style="width: 20px" class="align-middle">No</th>
                                        <th style="width: 70px" class="align-middle">PO Number</th>
                                        <th style="width: 80px" class="align-middle">PO Date</th>
                                        <th class="align-middle">Supplier</th>
                                        <th style="width: 130px" class="align-middle">Warehouse</th>
                                        <th style="width: 100px" class="align-middle">Grand Total</th>
                                        <th style="width: 80px" class="align-middle">Status</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($purchaseOrders as $key => $purchaseOrder)
                                            <tr class="text-dark">
                                                <td class="align-middle text-center">{{ ++$key }}</td>
                                                <td>
                                                    <button type="submit" formaction="" formmethod="POST" class="btn btn-sm btn-link text-bold">
                                                        {{ $purchaseOrder->number }}
                                                    </button>
                                                </td>
                                                <td class="text-center align-middle">{{ formatDate($purchaseOrder->date, 'd-M-y')  }}</td>
                                                <td class="align-middle">{{ $purchaseOrder->supplier_name }}</td>
                                                <td class="align-middle">{{ $purchaseOrder->warehouse_name }}</td>
                                                <td class="text-right align-middle">{{ formatCurrency($purchaseOrder->grand_total) }}</td>
                                                <td class="text-center align-middle">{{ getPurchaseOrderStatusLabel($purchaseOrder->status) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-bold text-dark h4 py-2">No Data Available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
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
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script type="text/javascript">
        let datatable = $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    </script>
@endpush
