@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Cetak Barang Masuk</h1>
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
                    <div class="card show">
                        <div class="card-body">
                            <form action="{{ route('goods-receipts.print', 0) }}" method="GET" id="form">
                                @csrf
                                <div class="tab-content" id="tabContent">
                                    <div class="tab-pane fade show active" id="notPrinted" role="tabpanel" aria-labelledby="notPrintedTab">
                                        <div class="form-group row justify-content-center">
                                            <label for="startNumber" class="col-auto col-form-label text-bold">Nomor BM</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-2">
                                                <select class="selectpicker print-transaction-select-picker" name="start_number" id="startNumber" data-live-search="true" data-size="6" title="Pilih Nomor Awal" required>
                                                    @foreach($goodsReceipts as $goodsReceipt)
                                                        <option value="{{ $goodsReceipt->id }}" data-tokens="{{ $goodsReceipt->number }}">{{ $goodsReceipt->number }}</option>
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
                                                    <th class="align-middle th-goods-receipt-number-index">Nomor</th>
                                                    <th class="align-middle th-goods-receipt-date-index">Tanggal</th>
                                                    <th class="align-middle th-goods-receipt-branch-index-print">Cabang</th>
                                                    <th class="align-middle">Supplier</th>
                                                    <th class="align-middle th-goods-receipt-warehouse-index-print">Gudang</th>
                                                    <th class="align-middle th-goods-receipt-invoice-age-index">Umur Nota</th>
                                                    <th class="align-middle th-goods-receipt-grand-total-index">Grand Total</th>
                                                    <th class="align-middle th-goods-receipt-status-index">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemNotPrinted">
                                                @forelse ($goodsReceipts as $key => $goodsReceipt)
                                                    <tr class="text-dark">
                                                        <td class="align-middle text-center">{{ ++$key }}</td>
                                                        <td class="align-middle">
                                                            <a href="{{ route('goods-receipts.detail', $goodsReceipt->id) }}" class="btn btn-sm btn-link text-bold">
                                                                {{ $goodsReceipt->number }}
                                                            </a>
                                                        </td>
                                                        <td class="text-center align-middle" data-sort="{{ formatDate($goodsReceipt->date, 'Ymd') }}">{{ formatDate($goodsReceipt->date, 'd-M-y')  }}</td>
                                                        <td class="align-middle">{{ $goodsReceipt->branch_name }}</td>
                                                        <td class="align-middle">{{ $goodsReceipt->supplier_name }}</td>
                                                        <td class="align-middle">{{ $goodsReceipt->warehouse_name }}</td>
                                                        <td class="text-center align-middle" data-sort="{{ getInvoiceAge($goodsReceipt->date, $goodsReceipt->tempo) }}">{{ getInvoiceAge($goodsReceipt->date, $goodsReceipt->tempo) }} Hari</td>
                                                        <td class="text-right align-middle" data-sort="{{ $goodsReceipt->grand_total }}">{{ formatPrice($goodsReceipt->grand_total) }}</td>
                                                        <td class="text-center align-middle">{{ getGoodsReceiptStatusLabel($goodsReceipt->status) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <input type="hidden" name="is_printed" id="isPrinted" value="0">
                                    </div>
                                    <div class="tab-pane fade" id="printed" role="tabpanel" aria-labelledby="printedTab">
                                        <div class="form-group row justify-content-center">
                                            <label for="startNumber" class="col-auto col-form-label text-bold">Nomor BM</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-2">
                                                <select class="selectpicker print-transaction-select-picker" name="start_number_printed" id="startNumberPrinted" data-live-search="true" data-size="6" title="Pilih Nomor Awal">
                                                    @foreach($goodsReceipts as $goodsReceipt)
                                                        <option value="{{ $goodsReceipt->id }}" data-tokens="{{ $goodsReceipt->number }}">{{ $goodsReceipt->number }}</option>
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
                                                    <th class="align-middle th-goods-receipt-number-index">Nomor</th>
                                                    <th class="align-middle th-goods-receipt-date-index">Tanggal</th>
                                                    <th class="align-middle th-goods-receipt-branch-index-print">Cabang</th>
                                                    <th class="align-middle">Supplier</th>
                                                    <th class="align-middle th-goods-receipt-warehouse-index-print">Gudang</th>
                                                    <th class="align-middle th-goods-receipt-invoice-age-index">Umur Nota</th>
                                                    <th class="align-middle th-goods-receipt-grand-total-index">Grand Total</th>
                                                    <th class="align-middle th-goods-receipt-status-index">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemPrinted">
                                            </tbody>
                                        </table>
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
            "columnDefs": [
                {
                    targets: [8],
                    orderable: false
                }
            ],
        });

        let datatablePrinted = $('#dataTablePrinted').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</span>`
            },
            "columnDefs": [
                {
                    targets: [8],
                    orderable: false
                }
            ],
        });

        $(document).ready(function() {
            let printedGoodsReceipts;
            let notPrintedGoodsReceipts = @json($goodsReceipts);
            let notPrintedTab = $('#notPrintedTab');
            let printedTab = $('#printedTab');

            notPrintedTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemNotPrinted');
                if(table.find('.item-row').length === 0) {
                    displayGoodsReceiptData(table, 9, notPrintedTab, datatableNotPrinted, 0);
                }

                removeRequiredStartNumberElement($('#startNumberPrinted'), 0);
            });

            printedTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemPrinted');
                if(table.find('.item-row').length === 0) {
                    displayGoodsReceiptData(table, 9, printedTab, datatablePrinted, 1);
                }

                removeRequiredStartNumberElement($('#startNumber'), 1);
            });

            $('#startNumber').on('change', function (event) {
                let selectedValue = $(this).val();
                let finalNumber = $('#finalNumber');

                handleNumberChange(notPrintedGoodsReceipts, selectedValue, finalNumber);
            });

            $('#startNumberPrinted').on('change', function (event) {
                let selectedValue = $(this).val();
                let finalNumber = $('#finalNumberPrinted');

                handleNumberChange(printedGoodsReceipts, selectedValue, finalNumber);
            });

            function displayGoodsReceiptData(table, colspan, tabItem, datatable, isPrinted) {
                $.ajax({
                    url: '{{ route('goods-receipts.index-print-ajax') }}',
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
                        let goodsReceipts = data.data;
                        table.empty();

                        if(isPrinted) {
                           printedGoodsReceipts = goodsReceipts;
                        } else {
                            notPrintedGoodsReceipts = goodsReceipts;
                        }

                        if(goodsReceipts.length === 0) {
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

                            $.each(goodsReceipts, function(index, item) {
                                newRow = goodsReceiptRow(rowNumber, item);

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

                                if (goodsReceipts.length > 0) {
                                    datatable.rows.add(table.find('tr'));
                                }

                                datatable.draw(false);
                            }
                        }
                    },
                })
            }

            function goodsReceiptRow(rowNumber, item) {
                let baseUrl = `{{ route('goods-receipts.detail', 'id') }}`;
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
                        <td class="align-middle">${item.branch_name}</td>
                        <td class="align-middle">${item.supplier_name}</td>
                        <td class="align-middle">${item.warehouse_name}</td>
                        <td class="align-middle text-center">${item.warehouse_name}</td>
                        <td class="align-middle text-center" data-sort="${item.grand_total}">${thousandSeparator(item.grand_total)}</td>
                        <td class="align-middle text-center">${getGoodsReceiptStatusLabel(item.status)}</td>
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

            function handleNumberChange(goodsReceiptData, selectedValue, finalElement) {
                const filteredGoodsReceipts = goodsReceiptData.filter(item => item.id > selectedValue);
                finalElement.empty();

                if(filteredGoodsReceipts.length === 0) {
                    finalElement.attr('disabled', true);
                } else {
                    $.each(filteredGoodsReceipts, function(key, item) {
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

            function getGoodsReceiptStatusLabel(status) {
                const labels = {
                    'ACTIVE': 'Aktif',
                    'UPDATED': 'Update',
                    'CANCEL': 'Batal',
                };

                return labels[status];
            }
        });
    </script>
@endpush
