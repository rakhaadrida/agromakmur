@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Create Purchase Return</h1>
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
                <div class="table-responsive table-responsive-return">
                    <div class="card show">
                        <div class="card-body">
                            <form action="{{ route('purchase-returns.store') }}" method="POST" id="form">
                                @csrf
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Return Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold" name="number" id="number" value="{{ old('number') }}" tabindex="1" autofocus required >
                                                </div>
                                                <label for="date" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ $date }}" tabindex="2" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row delivery-order-customer-input">
                                        <label for="goodsReceiptId" class="col-2 col-form-label text-bold text-right">Receipt Number</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <select class="selectpicker warehouse-select-picker" name="goods_receipt_id" id="goodsReceiptId" data-live-search="true" data-size="6" title="Enter or Choose Number" tabindex="3" required>
                                                @foreach($goodsReceipts as $goodsReceipt)
                                                    <option value="{{ $goodsReceipt->id }}" data-tokens="{{ $goodsReceipt->number }}" data-supplier="{{ $goodsReceipt->supplier_id }}">{{ $goodsReceipt->number }}</option>
                                                @endforeach
                                            </select>
                                            @error('goods_receipt_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <label for="receivedDate" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Received Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="received_date" id="receivedDate" tabindex="4">
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="supplierId" class="col-2 col-form-label text-bold text-right">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <select class="selectpicker warehouse-select-picker" name="supplier_id" id="supplierId" data-live-search="true" data-size="6" title="Enter or Choose Supplier" tabindex="5" required>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}" data-tokens="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('supplier_id')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="itemContent" hidden>
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="align-middle table-head-number-delivery-order">No</td>
                                                <td class="align-middle table-head-code-delivery-order">SKU</td>
                                                <td class="align-middle">Product Name</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Receipt Qty</td>
                                                <td class="align-middle table-head-unit-delivery-order">Unit</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Return Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Received Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Cut Bill Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Remaining Qty</td>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTable">
                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="form-row justify-content-center">
                                        <div class="col-2">
                                             <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit" tabindex="10000">Submit</button>
                                        </div>
                                        <div class="col-2">
                                            <button type="reset" class="btn btn-outline-danger btn-block text-bold" id="btnReset" tabindex="10001">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalEmptyQuantity" tabindex="-1" role="dialog" aria-labelledby="modalEmptyQuantity" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="h2 text-bold">&times;</span>
                    </button>
                    <h4 class="modal-title text-bold">Return Quantity Notification</h4>
                </div>
                <div class="modal-body text-dark">
                    <h5>Return Quantity can not be empty. Please enter a minimum quantity of 1 in one of the inputs.</h5>
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

            $('#supplierId').change(function() {
                let supplierId = $(this).val();
                displayGoodsReceiptList(supplierId);
            });

            $('#goodsReceiptId').change(function() {
                $('#itemContent').removeAttr('hidden');

                let goodsReceiptId = $(this).val();
                let supplierId = $(this).find(':selected').data('supplier');

                $('#supplierId').selectpicker('val', supplierId);
                displayGoodsReceiptData(goodsReceiptId);
            });

            table.on('keypress', 'input[name="quantity[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();

                    let quantity = $(`#quantity-${index}`);
                    quantity.attr('title', 'Only allowed to input numbers');
                    quantity.attr('data-original-title', 'Only allowed to input numbers');
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
                    deliveredQuantity.attr('title', 'Only allowed to input numbers');
                    deliveredQuantity.attr('data-original-title', 'Only allowed to input numbers');
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
                    cutBillQuantity.attr('title', 'Only allowed to input numbers');
                    cutBillQuantity.attr('data-original-title', 'Only allowed to input numbers');
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

                quantities.each(function() {
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
                            let receivedQuantity = $(`#receivedQuantity-${index}`);
                            receivedQuantity.attr('title', 'Received Quantity can not greater than remaining quantity');
                            receivedQuantity.attr('data-original-title', 'Received Quantity can not greater than remaining quantity');
                            receivedQuantity.tooltip('show');
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

            function displayGoodsReceiptList(supplierId) {
                $.ajax({
                    url: '{{ route('goods-receipts.index-list-ajax') }}',
                    type: 'GET',
                    data: {
                        supplier_id: supplierId,
                    },
                    dataType: 'json',
                    success: function(data) {
                        let goodsReceipt = $('#goodsReceiptId');
                        goodsReceipt.empty();

                        $.each(data.data, function(index, item) {
                            goodsReceipt.append(
                                $('<option></option>', {
                                    value: item.id,
                                    text: item.number,
                                    'data-tokens': item.number,
                                })
                            );

                            if(!index) {
                                goodsReceipt.selectpicker({
                                    title: 'Enter or Choose Number'
                                });
                            }

                            goodsReceipt.selectpicker('refresh');
                            goodsReceipt.selectpicker('render');
                        });
                    },
                })
            }

            function displayGoodsReceiptData(goodsReceiptId) {
                $.ajax({
                    url: '{{ route('goods-receipts.index-data-ajax') }}',
                    type: 'GET',
                    data: {
                        goods_receipt_id: goodsReceiptId,
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#supplierId').val(data.data.supplier_id);
                        $('#supplier').val(data.data.supplier_name);

                        let goodsReceiptItems = data.goods_receipt_items;
                        let rowId = 0;
                        let rowNumber = 1;
                        let rowNumbers = 5;

                        table.empty();
                        $.each(goodsReceiptItems, function(index, item) {
                            let newRow = goodsReceiptItemRowElement(rowId, rowNumber, rowNumbers, item);
                            table.append(newRow);

                            rowId++;
                            rowNumber++;
                            rowNumbers += 4;
                        });
                    },
                })
            }

            function goodsReceiptItemRowElement(rowId, rowNumber, rowNumbers, item) {
                return `
                    <tr class="text-bold text-dark" id="${rowId}">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_sku[]" id="productSku-${rowId}" value="${item.product_sku}" title="" readonly>
                            <input type="hidden" name="product_id[]" id="productId-${rowId}" value="${item.product_id}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_name[]" id="productName-${rowId}" value="${item.product_name}" title="" readonly>
                            <input type="hidden" name="item_id[]" id="itemId-${rowId}" value="${item.id}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="order_quantity[]" id="orderQuantity-${rowId}" value="${thousandSeparator(item.quantity)}" title="" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-center readonly-input" name="unit[]" id="unit-${rowId}" value="${item.unit_name}" title="" readonly>
                            <input type="hidden" name="unit_id[]" id="unitId-${rowId}" value="${item.unit_id}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="quantity[]" id="quantity-${rowId}" value="" tabindex="${rowNumbers + 1}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers">
                            <input type="hidden" name="real_quantity[]" id="realQuantity-${rowId}" value="${item.actual_quantity / item.quantity}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="received_quantity[]" id="receivedQuantity-${rowId}" tabindex="${rowNumbers + 2}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="cut_bill_quantity[]" id="cutBillQuantity-${rowId}" tabindex="${rowNumbers + 3}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="remaining_quantity[]" id="remainingQuantity-${rowId}" title="" readonly>
                        </td>
                    </tr>
                `;
            }

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
