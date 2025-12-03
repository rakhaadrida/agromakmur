@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Cetak Transfer Produk</h1>
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
                            <form action="{{ route('product-transfers.print', 0) }}" method="GET" id="form">
                                @csrf
                                <div class="tab-content" id="tabContent">
                                    <div class="tab-pane fade show active" id="notPrinted" role="tabpanel" aria-labelledby="notPrintedTab">
                                        <div class="form-group row justify-content-center">
                                            <label for="startNumber" class="col-auto col-form-label text-bold">Nomor Transfer</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-2">
                                                <select class="selectpicker print-transaction-select-picker" name="start_number" id="startNumber" data-live-search="true" data-size="6" title="Pilih Nomor Awal" required>
                                                    @foreach($productTransfers as $productTransfer)
                                                        <option value="{{ $productTransfer->id }}" data-tokens="{{ $productTransfer->number }}">{{ $productTransfer->number }}</option>
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
                                                    <th class="align-middle th-code-transaction-index">Nomor</th>
                                                    <th class="align-middle th-date-transaction-index">Tanggal</th>
                                                    <th class="align-middle th-status-transaction-index">Status</th>
                                                    <th class="align-middle th-status-transaction-index">Admin</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemNotPrinted">
                                                @forelse ($productTransfers as $key => $productTransfer)
                                                    <tr class="text-dark">
                                                        <td class="align-middle text-center">{{ ++$key }}</td>
                                                        <td>
                                                            <a href="{{ route('product-transfers.detail', $productTransfer->id) }}" class="btn btn-sm btn-link text-bold">
                                                                {{ $productTransfer->number }}
                                                            </a>
                                                        </td>
                                                        <td class="text-center align-middle" data-sort="{{ formatDate($productTransfer->date, 'Ymd') }}">{{ formatDate($productTransfer->date, 'd-M-y')  }}</td>
                                                        <td class="text-center align-middle">{{ getProductTransferStatusLabel($productTransfer->status) }}</td>
                                                        <td class="align-middle">{{ $productTransfer->user_name }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <input type="hidden" name="is_printed" id="isPrinted" value="0">
                                    </div>
                                    <div class="tab-pane fade" id="printed" role="tabpanel" aria-labelledby="printedTab">
                                        <div class="form-group row justify-content-center">
                                            <label for="startNumber" class="col-auto col-form-label text-bold">Nomor Transfer</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-2">
                                                <select class="selectpicker print-transaction-select-picker" name="start_number_printed" id="startNumberPrinted" data-live-search="true" data-size="6" title="Pilih Nomor Awal">
                                                    @foreach($productTransfers as $productTransfer)
                                                        <option value="{{ $productTransfer->id }}" data-tokens="{{ $productTransfer->number }}">{{ $productTransfer->number }}</option>
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
                                                <th class="align-middle th-code-transaction-index">Nomor</th>
                                                <th class="align-middle th-date-transaction-index">Tanggal</th>
                                                <th class="align-middle th-status-transaction-index">Status</th>
                                                <th class="align-middle th-status-transaction-index">Admin</th>
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
            let printedProductTransfers;
            let notPrintedProductTransfers = @json($productTransfers);
            let notPrintedTab = $('#notPrintedTab');
            let printedTab = $('#printedTab');

            notPrintedTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemNotPrinted');
                if(table.find('.item-row').length === 0) {
                    displayProductTransferData(table, 5, notPrintedTab, datatableNotPrinted, 0);
                }

                removeRequiredStartNumberElement($('#startNumberPrinted'), 0);
            });

            printedTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemPrinted');
                if(table.find('.item-row').length === 0) {
                    displayProductTransferData(table, 5, printedTab, datatablePrinted, 1);
                }

                removeRequiredStartNumberElement($('#startNumber'), 1);
            });

            $('#startNumber').on('change', function (event) {
                let selectedValue = $(this).val();
                let finalNumber = $('#finalNumber');

                handleNumberChange(notPrintedProductTransfers, selectedValue, finalNumber, 0);
            });

            $('#startNumberPrinted').on('change', function (event) {
                let selectedValue = $(this).val();
                let finalNumber = $('#finalNumberPrinted');

                handleNumberChange(printedProductTransfers, selectedValue, finalNumber, 1);
            });

            function displayProductTransferData(table, colspan, tabItem, datatable, isPrinted) {
                $.ajax({
                    url: '{{ route('product-transfers.index-print-ajax') }}',
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
                        let productTransfers = data.data;
                        table.empty();

                        if(isPrinted) {
                            printedProductTransfers = productTransfers;
                        } else {
                            notPrintedProductTransfers = productTransfers;
                        }

                        if(productTransfers.length === 0) {
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
                            let newRow;

                            $.each(productTransfers, function(index, item) {
                                newRow = productTransferRow(rowNumber, item);

                                table.append(newRow);
                                rowNumber++;

                                if(isPrinted) {
                                    displayNumberData(startNumberPrinted, index, item)
                                } else {
                                    displayNumberData(startNumber, index, item);
                                }
                            });

                            if(isPrinted) {
                                startNumberPrinted.attr('required', true);
                                disableFinalNumberElement($('#finalNumberPrinted'));
                            } else {
                                startNumber.attr('required', true);
                                disableFinalNumberElement($('#finalNumber'));
                            }

                            if (datatable) {
                                datatable.clear();

                                if (productTransfers.length > 0) {
                                    datatable.rows.add(table.find('tr'));
                                }

                                datatable.draw(false);
                            }
                        }
                    },
                })
            }

            function productTransferRow(rowNumber, item) {
                let baseUrl = `{{ route('product-transfers.detail', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.id);

                return `
                    <tr class="text-dark item-row">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td class="align-middle">
                            <a href="${urlDetail}" class="btn btn-sm btn-link text-bold">
                                ${item.number}
                            </a>
                        </td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.date, 'Ymd')}">${formatDate(item.date, 'd-M-y')}</td>
                        <td class="align-middle text-center">${getProductTransferStatusLabel(item.status)}</td>
                        <td class="align-middle">${item.user_name}</td>
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
                    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: '2-digit' });
                }

                return dateStr;
            }

            function displayNumberData(element, index, item) {
                element.append(
                    $('<option></option>', {
                        value: item.id,
                        text: item.number,
                        'data-tokens': item.number,
                    })
                );

                if(!index) {
                    element.selectpicker({
                        title: 'Pilih Nomor Awal'
                    });
                }

                element.selectpicker('refresh');
                element.selectpicker('render');
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

            function handleNumberChange(productTransferData, selectedValue, finalElement, isPrinted) {
                let filteredProductTransfers;

                if(isPrinted) {
                    filteredProductTransfers = productTransferData.filter(item => item.id < selectedValue);
                } else {
                    filteredProductTransfers = productTransferData.filter(item => item.id > selectedValue);
                }

                finalElement.empty();

                if(filteredProductTransfers.length === 0) {
                    finalElement.attr('disabled', true);
                } else {
                    $.each(filteredProductTransfers, function(key, item) {
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

            function getProductTransferStatusLabel(status) {
                const labels = {
                    'ACTIVE': 'Aktif',
                    'CANCELLED': 'Batal',
                };

                return labels[status];
            }
        });
    </script>
@endpush
