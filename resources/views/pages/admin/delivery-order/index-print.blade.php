@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Cetak Surat Jalan</h1>
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
                            <form action="{{ route('delivery-orders.print', 0) }}" method="GET" id="form">
                                @csrf
                                <div class="container so-container">
                                    <div class="form-group row justify-content-center">
                                        <label for="startNumber" class="col-auto col-form-label text-bold">Nomor SJ</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <select class="selectpicker print-transaction-select-picker" name="start_number" id="startNumber" data-live-search="true" data-size="6" title="Pilih Nomor Awal" required>
                                                @foreach($deliveryOrders as $deliveryOrder)
                                                    <option value="{{ $deliveryOrder->id }}" data-tokens="{{ $deliveryOrder->number }}">{{ $deliveryOrder->number }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="finalNumber" class="col-auto col-form-label text-bold ">s / d</label>
                                        <div class="col-2">
                                            <select class="selectpicker print-transaction-final-select-picker" name="final_number" id="finalNumber" data-live-search="true" data-size="6" title="Pilih Nomor Akhir" disabled>
                                            </select>
                                        </div>
                                        <div class="col-2 mt-1 main-transaction-button">
                                            <button type="submit" class="btn btn-success btn-sm btn-block text-bold">Cetak</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-number-transaction-index">No</th>
                                            <th class="align-middle th-delivery-order-number-index">Nomor</th>
                                            <th class="align-middle th-delivery-order-date-index">Tanggal</th>
                                            <th class="align-middle th-delivery-order-number-index">Nomor SO</th>
                                            <th class="align-middle th-delivery-order-branch-index-print">Cabang</th>
                                            <th class="align-middle">Customer</th>
                                            <th class="align-middle th-delivery-order-status-index">Status</th>
                                            <th class="align-middle th-delivery-order-status-index">Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($deliveryOrders as $key => $deliveryOrder)
                                            <tr class="text-dark">
                                                <td class="align-middle text-center">{{ ++$key }}</td>
                                                <td class="align-middle">
                                                    <a href="{{ route('delivery-orders.detail', $deliveryOrder->id) }}" class="btn btn-sm btn-link text-bold">
                                                        {{ $deliveryOrder->number }}
                                                    </a>
                                                </td>
                                                <td class="text-center align-middle" data-sort="{{ formatDate($deliveryOrder->date, 'Ymd') }}">{{ formatDate($deliveryOrder->date, 'd-M-y')  }}</td>
                                                <td class="text-center align-middle">
                                                    <a href="{{ route('sales-orders.detail', $deliveryOrder->sales_order_id) }}" class="btn btn-sm btn-link text-bold">
                                                        {{ $deliveryOrder->sales_order_number }}
                                                    </a>
                                                </td>
                                                <td class="align-middle">{{ $deliveryOrder->branch_name }}</td>
                                                <td class="align-middle">{{ $deliveryOrder->customer_name }}</td>
                                                <td class="text-center align-middle">{{ getDeliveryOrderStatusLabel($deliveryOrder->status) }}</td>
                                                <td class="text-center align-middle">{{ $deliveryOrder->user_name }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</td>
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

        $(document).ready(function() {
            let deliveryOrders = @json($deliveryOrders);

            $('#startNumber').on('change', function (event) {
                let selectedValue = $(this).val();
                let finalNumber = $('#finalNumber');

                const filteredDeliveryOrders = deliveryOrders.filter(item => item.id > selectedValue);
                finalNumber.empty();

                if(filteredDeliveryOrders.length === 0) {
                    finalNumber.attr('disabled', true);
                } else {
                    $.each(filteredDeliveryOrders, function(key, item) {
                        finalNumber.append(
                            $('<option></option>', {
                                value: item.id,
                                text: item.number,
                                'data-tokens': item.number,
                            })
                        );
                    });

                    finalNumber.attr('disabled', false);
                }

                finalNumber.selectpicker('refresh');
            });
        });
    </script>
@endpush
