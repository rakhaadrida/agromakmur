@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Print Plan Order</h1>
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
                            <form action="{{ route('plan-orders.print', 0) }}" method="GET" id="form">
                                @csrf
                                <div class="container so-container">
                                    <div class="form-group row justify-content-center">
                                        <label for="startNumber" class="col-auto col-form-label text-bold">PO Number</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <select class="selectpicker print-transaction-select-picker" name="start_number" id="startNumber" data-live-search="true" data-size="6" title="Select Start Number" required>
                                                @foreach($planOrders as $planOrder)
                                                    <option value="{{ $planOrder->id }}" data-tokens="{{ $planOrder->number }}">{{ $planOrder->number }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="finalNumber" class="col-auto col-form-label text-bold ">up to</label>
                                        <div class="col-2">
                                            <select class="selectpicker print-transaction-final-select-picker" name="final_number" id="finalNumber" data-live-search="true" data-size="6" title="Select Final Number" disabled>
                                            </select>
                                        </div>
                                        <div class="col-2 mt-1 main-transaction-button">
                                            <button type="submit" class="btn btn-success btn-sm btn-block text-bold">Print</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-number-transaction-index">No</th>
                                            <th class="align-middle th-code-transaction-index">Number</th>
                                            <th class="align-middle th-date-transaction-index">Date</th>
                                            <th class="align-middle th-plan-order-branch-index-print">Branch</th>
                                            <th class="align-middle th-name-transaction-index">Supplier</th>
                                            <th class="align-middle th-plan-order-total-items-index-print">Total Items</th>
                                            <th class="align-middle th-plan-order-grand-total-index">Grand Total</th>
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
                                                <td class="align-middle text-center" data-sort="{{ formatDate($planOrder->date, 'Ymd') }}">{{ formatDate($planOrder->date, 'd-M-y')  }}</td>
                                                <td class="align-middle">{{ $planOrder->branch_name }}</td>
                                                <td class="align-middle">{{ $planOrder->supplier_name }}</td>
                                                <td class="align-middle text-center" data-sort="{{ $planOrder->planOrderItems->count() }}">{{ formatQuantity($planOrder->planOrderItems->count()) }}</td>
                                                <td class="align-middle text-right" data-sort="{{ $planOrder->grand_total }}">{{ formatPrice($planOrder->grand_total) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-bold text-dark h4 py-2">No Data Available</td>
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
            let planOrders = @json($planOrders);

            $('#startNumber').on('change', function (event) {
                let selectedValue = $(this).val();
                let finalNumber = $('#finalNumber');

                const filteredPlanOrders = planOrders.filter(item => item.id > selectedValue);
                finalNumber.empty();

                if(filteredPlanOrders.length === 0) {
                    finalNumber.attr('disabled', true);
                } else {
                    $.each(filteredPlanOrders, function(key, item) {
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
