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
                <ul class="nav nav-tabs" id="tabHeader" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive active" id="notPrintedTab" data-toggle="pill" data-target="#notPrinted" type="button" role="tab" aria-controls="not-printed" aria-selected="true">Belum Cetak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="printedTab" data-toggle="pill" data-target="#printed" type="button" role="tab" aria-controls="printed" aria-selected="false">Sudah Cetak</a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <div class="card show card-tabs">
                        <div class="card-body">
                            <form action="{{ route('delivery-orders.print', 0) }}" method="GET" id="form">
                                @csrf
                                <div class="tab-content" id="tabContent">
                                    <div class="tab-pane fade show active" id="notPrinted" role="tabpanel" aria-labelledby="notPrintedTab">
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
                                        <hr>
                                        <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTableNotPrinted">
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
                                            <tbody id="itemNotPrinted">
                                                @foreach ($deliveryOrders as $key => $deliveryOrder)
                                                    <tr class="text-dark">
                                                        <td class="align-middle text-center">{{ ++$key }}</td>
                                                        <td class="align-middle">
                                                            <a href="{{ route('delivery-orders.detail', $deliveryOrder->id) }}" class="btn btn-sm btn-link text-bold">
                                                                {{ $deliveryOrder->number }}
                                                            </a>
                                                        </td>
                                                        <td class="text-center align-middle" data-sort="{{ formatDate($deliveryOrder->date, 'Ymd') }}">{{ formatDateIso($deliveryOrder->date, 'DD-MMM-YY')  }}</td>
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
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <input type="hidden" name="is_printed" id="isPrinted" value="0">
                                    </div>
                                    <div class="tab-pane fade" id="printed" role="tabpanel" aria-labelledby="printedTab">
                                        <div class="form-group row justify-content-center">
                                            <label for="startNumber" class="col-auto col-form-label text-bold">Nomor SJ</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-2">
                                                <select class="selectpicker print-transaction-select-picker" name="start_number_printed" id="startNumberPrinted" data-live-search="true" data-size="6" title="Pilih Nomor Awal">
                                                    @foreach($deliveryOrders as $deliveryOrder)
                                                        <option value="{{ $deliveryOrder->id }}" data-tokens="{{ $deliveryOrder->number }}">{{ $deliveryOrder->number }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label for="finalNumber" class="col-auto col-form-label text-bold ">s / d</label>
                                            <div class="col-2">
                                                <select class="selectpicker print-transaction-final-select-picker" name="final_number_printed" id="finalNumberPrinted" data-live-search="true" data-size="6" title="Pilih Nomor Akhir" disabled>
                                                </select>
                                            </div>
                                            <div class="col-2 mt-1 main-transaction-button">
                                                <button type="submit" class="btn btn-success btn-sm btn-block text-bold">Cetak</button>
                                            </div>
                                        </div>
                                        <hr>
                                        <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTablePrinted">
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
                                            <tbody id="itemPrinted">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
        $.fn.dataTable.ext.order['dom-data-sort'] = function (settings, col) {
            return this.api()
                .column(col, { order: 'index' })
                .nodes()
                .map(function (td) {
                    return $(td).data('sort');
                });
        };

        let datatableNotPrinted = $('#dataTableNotPrinted').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</span>`
            },
        });

        let datatablePrinted = $('#dataTablePrinted').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</span>`
            },
        });

        $(document).ready(function() {
            let printedDeliveryOrders;
            let notPrintedDeliveryOrders = @json($deliveryOrders);
            let notPrintedTab = $('#notPrintedTab');
            let printedTab = $('#printedTab');

            notPrintedTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemNotPrinted');
                if(table.find('.item-row').length === 0) {
                    displayDeliveryOrderData(table, 8, notPrintedTab, datatableNotPrinted, 0);
                }

                removeRequiredStartNumberElement($('#startNumberPrinted'), 0);
            });

            printedTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemPrinted');
                if(table.find('.item-row').length === 0) {
                    displayDeliveryOrderData(table, 8, printedTab, datatablePrinted, 1);
                }

                removeRequiredStartNumberElement($('#startNumber'), 1);
            });

            $('#startNumber').on('change', function (event) {
                let selectedValue = $(this).val();
                let finalNumber = $('#finalNumber');

                handleNumberChange(notPrintedDeliveryOrders, selectedValue, finalNumber, 0);
            });

            $('#startNumberPrinted').on('change', function (event) {
                let selectedValue = $(this).val();
                let finalNumber = $('#finalNumberPrinted');

                handleNumberChange(printedDeliveryOrders, selectedValue, finalNumber, 1);
            });

            function displayDeliveryOrderData(table, colspan, tabItem, datatable, isPrinted) {
                $.ajax({
                    url: '{{ route('delivery-orders.index-print-ajax') }}',
                    type: 'GET',
                    data: {
                        is_printed: isPrinted,
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        table.empty();

                        let loadingRow = loadingItemRow(colspan);
                        table.append(loadingRow);
                    },
                    success: function(data) {
                        let deliveryOrders = data.data;
                        table.empty();

                        if(isPrinted) {
                            printedDeliveryOrders = deliveryOrders;
                        } else {
                            notPrintedDeliveryOrders = deliveryOrders;
                        }

                        if(deliveryOrders.length === 0) {
                            datatable.clear();
                            datatable.draw(false);
                        } else {
                            let startNumber = $('#startNumber');
                            let startNumberPrinted = $('#startNumberPrinted');

                            if(isPrinted) {
                                startNumberPrinted.empty();
                            } else {
                                startNumber.empty();
                            }

                            let rowNumber = 1;
                            let rowsHtml = '';

                            $.each(deliveryOrders, function(index, item) {
                                rowsHtml += deliveryOrderRow(rowNumber, item);
                                rowNumber++;
                            });

                            table.html(rowsHtml);

                            let optionsHtml = '';
                            $.each(deliveryOrders, function(index, item) {
                                optionsHtml += `<option value="${item.id}" data-tokens="${item.number}">${item.number}</option>`;
                            });

                            if (isPrinted) {
                                startNumberPrinted.html(optionsHtml);
                                startNumberPrinted.selectpicker({
                                    title: 'Pilih Nomor Awal'
                                });

                                startNumberPrinted.selectpicker('refresh');
                                startNumberPrinted.attr('required', true);
                                disableFinalNumberElement($('#finalNumberPrinted'));
                            } else {
                                startNumber.html(optionsHtml);
                                startNumber.selectpicker({
                                    title: 'Pilih Nomor Awal'
                                });

                                startNumber.selectpicker('refresh');
                                startNumber.attr('required', true);
                                disableFinalNumberElement($('#finalNumber'));
                            }

                            if (datatable) {
                                datatable.clear();

                                if (deliveryOrders.length > 0) {
                                    datatable.rows.add(table.find('tr'));
                                }

                                datatable.draw(false);
                            }
                        }
                    },
                })
            }

            function deliveryOrderRow(rowNumber, item) {
                let baseUrl = `{{ route('delivery-orders.detail', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.id);

                let baseUrlSalesOrder = `{{ route('sales-orders.detail', 'id') }}`;
                let urlSalesOrderDetail = baseUrlSalesOrder.replace('id', item.sales_order_id);

                return `
                    <tr class="text-dark item-row">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td class="align-middle">
                            <a href="${urlDetail}" class="btn btn-sm btn-link text-bold">
                                ${item.number}
                            </a>
                        </td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.date, 'Ymd')}">${formatDate(item.date, 'd-M-y')}</td>
                        <td class="align-middle">
                            <a href="${urlSalesOrderDetail}" class="btn btn-sm btn-link text-bold">
                                ${item.sales_order_number}
                            </a>
                        </td>
                        <td class="align-middle">${item.branch_name}</td>
                        <td class="align-middle">${item.customer_name}</td>
                        <td class="align-middle text-center">${getDeliveryOrderStatusLabel(item.status)}</td>
                        <td class="align-middle text-center">${item.user_name}</td>
                    </tr>
                `;
            }

            function loadingItemRow(colspan) {
                return `
                    <tr>
                        <td colspan="${colspan}" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                        </td>
                    </tr>
                `;
            }

            function formatDate(dateStr, format = 'd-M-y') {
                const date = new Date(dateStr);

                if (format === 'Ymd') {
                    return date.toISOString().split('T')[0].replace(/-/g, '');
                } else if (format === 'd-M-y') {
                    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' });
                }

                return dateStr;
            }

            function disableFinalNumberElement(element) {
                element.empty();
                element.attr('disabled', true);
                element.selectpicker('refresh');
            }

            function removeRequiredStartNumberElement(element, number) {
                element.removeAttr('required');
                $('#isPrinted').val(number);
            }

            function handleNumberChange(deliveryOrderData, selectedValue, finalElement, isPrinted) {
                let filteredDeliveryOrders;

                if(isPrinted) {
                    filteredDeliveryOrders = deliveryOrderData.filter(item => item.id < selectedValue);
                } else {
                    filteredDeliveryOrders = deliveryOrderData.filter(item => item.id > selectedValue);
                }

                finalElement.empty();

                if(filteredDeliveryOrders.length === 0) {
                    finalElement.attr('disabled', true);
                } else {
                    $.each(filteredDeliveryOrders, function(key, item) {
                        finalElement.append(
                            $('<option></option>', {
                                value: item.id,
                                text: item.number,
                                'data-tokens': item.number,
                            })
                        );
                    });

                    finalElement.attr('disabled', false);
                }

                finalElement.selectpicker('refresh');
            }

            function getDeliveryOrderStatusLabel(status) {
                const labels = {
                    'ACTIVE': 'Aktif',
                    'UPDATED': 'Update',
                    'CANCELLED': 'Batal',
                };

                return labels[status];
            }
        });
    </script>
@endpush
