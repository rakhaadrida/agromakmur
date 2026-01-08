@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Ubah Retur Pembelian</h1>
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
                            <form action="{{ route('purchase-returns.update', $purchaseReturn->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Nomor Retur</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold" name="number" id="number" value="{{ $purchaseReturn->number }}" readonly>
                                                </div>
                                                <label for="date" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Tanggal</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ formatDate($purchaseReturn->date, 'd-m-Y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row delivery-order-customer-input">
                                        <label for="goodsReceipt" class="col-2 col-form-label text-bold text-right">Nomor BM</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="goods_receipt" id="goodsReceipt" value="{{ $purchaseReturn->goodsReceipt->number }}" readonly>
                                            <input type="hidden" name="goods_receipt_id" value="{{ $purchaseReturn->goods_receipt_id }}">
                                        </div>
                                        <label for="receivedDate" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Tanggal Terima</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="received_date" id="receivedDate" value="{{ $purchaseReturn->received_date ? formatDate($purchaseReturn->received_date, 'd-m-Y') : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="branch" class="col-2 col-form-label text-bold text-right">Cabang</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="branch" id="branch" value="{{ $purchaseReturn->goodsReceipt->branch->name }}" readonly>
                                        </div>
                                        <label for="receiptStatus" class="col-2 col-form-label text-bold text-right sales-order-middle-last-input">Status Terima</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="receipt_status" id="receiptStatus" value="{{ getPurchaseReturnReceiptStatusLabel($purchaseReturn->receipt_status) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="supplier" class="col-2 col-form-label text-bold text-right">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="supplier" id="supplier" value="{{ $purchaseReturn->supplier->name }}" readonly>
                                            <input type="hidden" name="supplier_id" value="{{ $purchaseReturn->supplier_id }}">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div>
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="align-middle table-head-number-delivery-order">No</td>
                                                <td class="align-middle table-head-code-delivery-order">SKU</td>
                                                <td class="align-middle">Nama Produk</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Qty Masuk</td>
                                                <td class="align-middle table-head-unit-delivery-order">Unit</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Qty Retur</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Qty Terima</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Potong Tagihan</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Sisa Qty</td>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTable">
                                            @foreach($purchaseReturn->purchaseReturnItems as $index => $purchaseReturnItem)
                                                <tr class="text-bold text-dark" id="{{ $index }}">
                                                    <td class="align-middle text-center" tabindex="0" data-row="{{ $index }}" data-col="0">{{ $index + 1 }}</td>
                                                    <td tabindex="0" data-row="{{ $index }}" data-col="1">
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_sku[]" id="productSku-{{ $index }}" value="{{ $purchaseReturnItem->product->sku }}" title="" readonly>
                                                        <input type="hidden" name="product_id[]" id="productId-{{ $index }}" value="{{ $purchaseReturnItem->product_id }}">
                                                    </td>
                                                    <td tabindex="0" data-row="{{ $index }}" data-col="2">
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_name[]" id="productName-{{ $index }}" value="{{ $purchaseReturnItem->product->name }}" title="" readonly>
                                                        <input type="hidden" name="item_id[]" id="itemId-{{ $index }}" value="{{ $purchaseReturnItem->goods_receipt_item_id }}">
                                                    </td>
                                                    <td tabindex="0" data-row="{{ $index }}" data-col="3">
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="order_quantity[]" id="orderQuantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->receipt_quantity) }}" title="" readonly>
                                                    </td>
                                                    <td tabindex="0" data-row="{{ $index }}" data-col="4">
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-center readonly-input" name="unit[]" id="unit-{{ $index }}" value="{{ $purchaseReturnItem->unit->name }}" title="" readonly>
                                                        <input type="hidden" name="unit_id[]" id="unitId-{{ $index }}" value="{{ $purchaseReturnItem->unit_id }}">
                                                    </td>
                                                    <td tabindex="0" data-row="{{ $index }}" data-col="5">
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="quantity[]" id="quantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->quantity) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" required>
                                                        <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $index }}" value="{{ $purchaseReturnItem->actual_quantity / $purchaseReturnItem->quantity }}">
                                                    </td>
                                                    <td tabindex="0" data-row="{{ $index }}" data-col="6">
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="received_quantity[]" id="receivedQuantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->received_quantity) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja">
                                                    </td>
                                                    <td tabindex="0" data-row="{{ $index }}" data-col="7">
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="cut_bill_quantity[]" id="cutBillQuantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->cut_bill_quantity) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja">
                                                    </td>
                                                    <td tabindex="0" data-row="{{ $index }}" data-col="8">
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="remaining_quantity[]" id="remainingQuantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->remaining_quantity) }}" title="" readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="form-row justify-content-center">
                                        @if(!isWaitingApproval($purchaseReturn->status) && !isCancelled($purchaseReturn->status))
                                            <div class="col-2">
                                                 <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit" tabindex="10000">Simpan</button>
                                            </div>
                                            <div class="col-2">
                                                <button type="button" class="btn btn-outline-danger btn-block text-bold" id="btnCancel" data-toggle="modal" data-target="#modalCancelReturn" data-id="{{ $purchaseReturn->id }}" tabindex="10001">Batalkan</button>
                                            </div>
                                        @endif
                                        <div class="col-2">
                                            <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Kembali ke Daftar</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="modal" id="modalCancelReturn" tabindex="-1" role="dialog" aria-labelledby="modalCancelReturn" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" class="h2 text-bold">&times;</span>
                                            </button>
                                            <h4 class="modal-title">Batalkan Retur - {{ $purchaseReturn->number }}</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('purchase-returns.destroy', $purchaseReturn->id) }}" method="POST" id="deleteForm">
                                                @csrf
                                                @method('DELETE')
                                                <div class="form-group row">
                                                    <label for="status" class="col-2 col-form-label text-bold">Status</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-3">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="status" id="status" value="CANCEL" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group subtotal-so">
                                                    <label for="description" class="col-form-label">Deskripsi</label>
                                                    <input type="text" class="form-control" name="description" id="description">
                                                </div>
                                                <hr>
                                                <div class="form-row justify-content-center">
                                                    <div class="col-3">
                                                        <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmitCancel">Simpan</button>
                                                    </div>
                                                    <div class="col-3">
                                                        <button type="button" class="btn btn-outline-secondary btn-block text-bold" data-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $.fn.datepicker.dates['id'] = {
            days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            daysShort: ['Mgu', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            daysMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            today: 'Hari Ini',
            clear: 'Kosongkan'
        };

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'id',
        });

        $(document).ready(function() {
            const table = $('#itemTable');
            const modalCancelReturn = $('#modalCancelReturn');

            modalCancelReturn.on('show.bs.modal', function (e) {
                $('#description').attr('required', true);
            })

            modalCancelReturn.on('hide.bs.modal', function (e) {
                $('#description').removeAttr('required');
            })

            $('#btnSubmitCancel').on('click', function(event) {
                event.preventDefault();

                let checkForm = document.getElementById('deleteForm').checkValidity();
                if(!checkForm) {
                    document.getElementById('deleteForm').reportValidity();
                    return false;
                }

                $('#deleteForm').submit();
            });

            table.on('keydown', 'td', function (e) {
                if ($(this).find('.bootstrap-select.show').length) {
                    return;
                }

                const $cell = $(this);

                if ($cell.hasClass('action-delete')) {
                    if (e.key === 'Enter' || e.key === 'Delete') {
                        e.preventDefault();

                        const index = $(this).closest('tr').index();
                        const deleteRow = $('.remove-transaction-table');

                        updateAllRowIndexes(index, deleteRow);

                        return;
                    }
                }

                if (e.key === 'Enter' || e.key === 'F2') {
                    e.preventDefault();

                    const $input = $cell.find('input, textarea, select').first();
                    if ($input.length) {
                        $input.focus();

                        if ($input.is('select')) {
                            $input.selectpicker('toggle');
                        }
                    }
                    return;
                }

                if (e.target === this && /^[0-9a-zA-Z]$/.test(e.key)) {
                    const $input = $cell.find('input[type="text"], textarea').first();

                    if ($input.length) {
                        e.preventDefault();
                        $input.focus();

                        $input.val(e.key);

                        const el = $input[0];
                        el.setSelectionRange(el.value.length, el.value.length);
                    }
                    return;
                }

                if (
                    $(e.target).is('input, textarea, select, button') ||
                    $(e.target).closest('.bootstrap-select').length ||
                    $(e.target).is('[contenteditable]')
                ) {
                    return;
                }

                if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                    e.preventDefault();
                }

                let row = parseInt($cell.data('row'));
                let col = parseInt($cell.data('col'));

                let targetRow = row;
                let targetCol = col;

                switch (e.key) {
                    case 'ArrowRight':
                        targetCol++;
                        break;
                    case 'ArrowLeft':
                        targetCol--;
                        break;
                    case 'ArrowDown':
                        targetRow++;
                        break;
                    case 'ArrowUp':
                        targetRow--;
                        break;
                    default:
                        return;
                }

                const target = $(`#itemTable td[data-row="${targetRow}"][data-col="${targetCol}"]`);

                if (target.length) {
                    target.focus();
                }
            });

            table.on('keydown', 'input, select, textarea', function (e) {
                if ($(e.target).closest('.bootstrap-select').length) {
                    return;
                }

                if (e.key === 'Escape') {
                    e.preventDefault();
                    $(this).closest('td').focus();

                    return;
                }

                if (e.key !== 'Enter') return;

                e.preventDefault();
                e.stopPropagation();

                const rowCount = $('#itemTable tr').length;
                const cell = $(this).closest('td');

                let row = parseInt(cell.data('row'));
                let col = parseInt(cell.data('col'));

                if(col === 7) {
                    if(row === rowCount - 1) {
                        col++;
                    } else {
                        col = 5;
                        row++;
                    }
                } else {
                    col++;
                }

                const target = $(`#itemTable td[data-row="${row}"][data-col="${col}"]`);

                if (target.length) {
                    target.focus();
                }
            });

            table.on('keypress', 'input[name="quantity[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();

                    let quantity = $(`#quantity-${index}`);
                    quantity.attr('title', 'Hanya masukkan angka saja');
                    quantity.attr('data-original-title', 'Hanya masukkan angka saja');
                    quantity.tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="quantity[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            table.on('blur', 'input[name="quantity[]"]', function () {
                const index = $(this).closest('tr').index();

                calculateRemainingQuantity(index);
            });

            table.on('keypress', 'input[name="received_quantity[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();

                    let deliveredQuantity = $(`#receivedQuantity-${index}`);
                    deliveredQuantity.attr('title', 'Hanya masukkan angka saja');
                    deliveredQuantity.attr('data-original-title', 'Hanya masukkan angka saja');
                    deliveredQuantity.tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="received_quantity[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            table.on('blur', 'input[name="received_quantity[]"]', function () {
                const index = $(this).closest('tr').index();

                calculateRemainingQuantity(index);
            });

            table.on('keypress', 'input[name="cut_bill_quantity[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();

                    let cutBillQuantity = $(`#cutBillQuantity-${index}`);
                    cutBillQuantity.attr('title', 'Hanya masukkan angka saja');
                    cutBillQuantity.attr('data-original-title', 'Hanya masukkan angka saja');
                    cutBillQuantity.tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="cut_bill_quantity[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            table.on('blur', 'input[name="cut_bill_quantity[]"]', function () {
                const index = $(this).closest('tr').index();

                calculateRemainingQuantity(index);
            });

            $('#btnSubmit').on('click', function(event) {
                event.preventDefault();

                let quantities = $('input[name="quantity[]"]');
                let receivedQuantities = $('input[name="received_quantity[]"]');
                let cutBillQuantities = $('input[name="cut_bill_quantity[]"]');
                let isEmptyReceivedQuantity = true;

                receivedQuantities.each(function(e) {
                    if (this.value > 0) {
                        isEmptyReceivedQuantity = false;
                        return false
                    }
                });

                if(!isEmptyReceivedQuantity) {
                    $('#receivedDate').prop('required', true);
                } else {
                    $('#receivedDate').prop('required', false);
                }

                let checkForm = document.getElementById('form').checkValidity();
                if(!checkForm) {
                    document.getElementById('form').reportValidity();
                    return false;
                }

                let isEmptyArrayValue = false;

                quantities.each(function(e) {
                    if (this.value) {
                        isEmptyArrayValue = true;
                        return false
                    }
                });

                if(!isEmptyArrayValue) {
                    $('#modalEmptyQuantity').modal('show');
                    return false;
                }

                let isInvalidQuantity = 0;
                quantities.each(function(index) {
                    let quantityAmount = numberFormat(this.value);

                    let orderQuantityElement = $(`#orderQuantity-${index}`);
                    let orderQuantity = numberFormat(orderQuantityElement.val());

                    if(quantityAmount > orderQuantity) {
                        let quantity = $(`#quantity-${index}`);
                        quantity.attr('title', 'Quantity to be sent can not greater than order quantity');
                        quantity.attr('data-original-title', 'Quantity to be sent can not greater than order quantity');
                        quantity.tooltip('show');
                        isInvalidQuantity = 1;

                        return false;
                    }
                });

                if(!isInvalidQuantity) {
                    receivedQuantities.each(function (index) {
                        let receivedAmount = numberFormat(this.value);

                        let quantityElement = $(`#quantity-${index}`);
                        let cutBillQuantityElement = $(`#cutBillQuantity-${index}`);
                        let remainingQuantity = numberFormat(quantityElement.val()) - numberFormat(cutBillQuantityElement.val());

                        if (receivedAmount > remainingQuantity) {
                            let deliveredQuantity = $(`#receivedQuantity-${index}`);
                            deliveredQuantity.attr('title', 'Received Quantity can not greater than remaining quantity');
                            deliveredQuantity.attr('data-original-title', 'Received Quantity can not greater than remaining quantity');
                            deliveredQuantity.tooltip('show');
                            isInvalidQuantity = 1;

                            return false;
                        }
                    });
                }

                if(!isInvalidQuantity) {
                    cutBillQuantities.each(function (index) {
                        let cutBillAmount = numberFormat(this.value);

                        let quantityElement = $(`#quantity-${index}`);
                        let receivedQuantityElement = $(`#receivedQuantity-${index}`);
                        let remainingQuantity = numberFormat(quantityElement.val()) - numberFormat(receivedQuantityElement.val());

                        if (cutBillAmount > remainingQuantity) {
                            let cutBillQuantity = $(`#cutBillQuantity-${index}`);
                            cutBillQuantity.attr('title', 'Cut Bill Quantity can not greater than remaining quantity');
                            cutBillQuantity.attr('data-original-title', 'Cut Bill Quantity can not greater than remaining quantity');
                            cutBillQuantity.tooltip('show');
                            isInvalidQuantity = 1;

                            return false;
                        }
                    });
                }

                if(!isInvalidQuantity) {
                    quantities.each(function() {
                        this.value = numberFormat(this.value);
                    });

                    receivedQuantities.each(function() {
                        this.value = numberFormat(this.value);
                    });

                    cutBillQuantities.each(function() {
                        this.value = numberFormat(this.value);
                    });

                    $('#form').submit();
                }
            });

            function calculateRemainingQuantity(index) {
                let quantity = document.getElementById(`quantity-${index}`);
                let remainingQuantity = document.getElementById(`remainingQuantity-${index}`);

                if(quantity.value) {
                    let receivedQuantity = document.getElementById(`receivedQuantity-${index}`);
                    let cutBillQuantity = document.getElementById(`cutBillQuantity-${index}`);

                    let remainingAmount = numberFormat(quantity.value) - numberFormat(receivedQuantity.value) - numberFormat(cutBillQuantity.value);
                    remainingQuantity.value = thousandSeparator(remainingAmount);
                } else {
                    remainingQuantity.value = '';
                }
            }

            function currencyFormat(value) {
                return value
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    ;
            }

            function numberFormat(value) {
                return +value.replace(/\./g, "");
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
        });
    </script>
@endpush
