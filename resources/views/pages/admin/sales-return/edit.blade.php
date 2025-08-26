@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Sales Return</h1>
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
                            <form action="{{ route('sales-returns.update', $salesReturn->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Return Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold" name="number" id="number" value="{{ $salesReturn->number }}" readonly>
                                                </div>
                                                <label for="date" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ formatDate($salesReturn->date, 'd-M-y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row delivery-order-customer-input">
                                        <label for="salesOrder" class="col-2 col-form-label text-bold text-right">Invoice Number</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="sales_order" id="salesOrder" value="{{ $salesReturn->salesOrder->number }}" readonly>
                                            <input type="hidden" name="sales_order_id" value="{{ $salesReturn->sales_order_id }}">
                                        </div>
                                        <label for="deliveryDate" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Delivery Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="delivery_date" id="deliveryDate" value="{{ $salesReturn->delivery_date ? formatDate($salesReturn->delivery_date, 'd-M-y') : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="customer" class="col-2 col-form-label text-bold text-right">Customer</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="customer" id="customer" value="{{ $salesReturn->customer->name }}" readonly>
                                            <input type="hidden" name="customer_id" value="{{ $salesReturn->customer_id }}">
                                        </div>
                                        <label for="deliveryStatus" class="col-2 col-form-label text-bold text-right sales-order-middle-last-input">Delivery Status</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="delivery_status" id="deliveryStatus" value="{{ getSalesReturnDeliveryStatusLabel($salesReturn->delivery_status) }}" readonly>
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
                                                <td class="align-middle">Product Name</td>
                                                <td class="align-middle table-head-unit-delivery-order">Unit</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Return Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Delivered Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Cut Bill Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Remaining Qty</td>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTable">
                                            @foreach($salesReturn->salesReturnItems as $index => $salesReturnItem)
                                                <tr class="text-bold text-dark" id="{{ $index }}">
                                                    <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_sku[]" id="productSku-{{ $index }}" value="{{ $salesReturnItem->product->sku }}" title="" readonly>
                                                        <input type="hidden" name="product_id[]" id="productId-{{ $index }}" value="{{ $salesReturnItem->product_id }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_name[]" id="productName-{{ $index }}" value="{{ $salesReturnItem->product->name }}" title="" readonly>
                                                        <input type="hidden" name="item_id[]" id="itemId-{{ $index }}" value="${item.id}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-center readonly-input" name="unit[]" id="unit-{{ $index }}" value="{{ $salesReturnItem->unit->name }}" title="" readonly>
                                                        <input type="hidden" name="unit_id[]" id="unitId-{{ $index }}" value="${item.unit_id}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="quantity[]" id="quantity-{{ $index }}" value="{{ formatQuantity($salesReturnItem->quantity) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" required>
                                                        <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $index }}" value="{{ $salesReturnItem->actual_quantity / $salesReturnItem->quantity }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="delivered_quantity[]" id="deliveredQuantity-{{ $index }}" value="{{ formatQuantity($salesReturnItem->delivered_quantity) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="cut_bill_quantity[]" id="cutBillQuantity-{{ $index }}" value="{{ formatQuantity($salesReturnItem->cut_bill_quantity) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="remaining_quantity[]" id="remainingQuantity-{{ $index }}" value="{{ formatQuantity($salesReturnItem->remaining_quantity) }}" title="" readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="form-row justify-content-center">
                                        <div class="col-2">
                                             <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit" tabindex="10000">Submit</button>
                                        </div>
                                        <div class="col-2">
                                            <button type="reset" class="btn btn-outline-danger btn-block text-bold" id="btnCancel" tabindex="10001">Cancel Return</button>
                                        </div>
                                        <div class="col-2">
                                            <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Back to List</a>
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

            table.on('keypress', 'input[name="delivered_quantity[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();

                    let deliveredQuantity = $(`#deliveredQuantity-${index}`);
                    deliveredQuantity.attr('title', 'Only allowed to input numbers');
                    deliveredQuantity.attr('data-original-title', 'Only allowed to input numbers');
                    deliveredQuantity.tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="delivered_quantity[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            table.on('blur', 'input[name="delivered_quantity[]"]', function () {
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

                let checkForm = document.getElementById('form').checkValidity();
                if(!checkForm) {
                    document.getElementById('form').reportValidity();
                    return false;
                }

                let quantities = $('input[name="quantity[]"]');
                let deliveredQuantities = $('input[name="delivered_quantity[]"]');
                let cutBillQuantities = $('input[name="cut_bill_quantity[]"]');
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
                    this.value = numberFormat(this.value);

                    let orderQuantityElement = $(`#orderQuantity-${index}`);
                    let orderQuantity = numberFormat(orderQuantityElement.val());

                    if(this.value > orderQuantity) {
                        let quantity = $(`#quantity-${index}`);
                        quantity.attr('title', 'Quantity to be sent can not greater than order quantity');
                        quantity.attr('data-original-title', 'Quantity to be sent can not greater than order quantity');
                        quantity.tooltip('show');
                        isInvalidQuantity = 1;

                        return false;
                    }
                });

                if(!isInvalidQuantity) {
                    deliveredQuantities.each(function (index) {
                        this.value = numberFormat(this.value);

                        let quantityElement = $(`#quantity-${index}`);
                        let cutBillQuantityElement = $(`#cutBillQuantity-${index}`);
                        let remainingQuantity = numberFormat(quantityElement.val()) - numberFormat(cutBillQuantityElement.val());

                        if (this.value > remainingQuantity) {
                            let deliveredQuantity = $(`#deliveredQuantity-${index}`);
                            deliveredQuantity.attr('title', 'Delivered Quantity can not greater than remaining quantity');
                            deliveredQuantity.attr('data-original-title', 'Delivered Quantity can not greater than remaining quantity');
                            deliveredQuantity.tooltip('show');
                            isInvalidQuantity = 1;

                            return false;
                        }
                    });
                }

                if(!isInvalidQuantity) {
                    cutBillQuantities.each(function (index) {
                        this.value = numberFormat(this.value);

                        let quantityElement = $(`#quantity-${index}`);
                        let deliveredQuantityElement = $(`#deliveredQuantity-${index}`);
                        let remainingQuantity = numberFormat(quantityElement.val()) - numberFormat(deliveredQuantityElement.val());

                        if (this.value > remainingQuantity) {
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

                    deliveredQuantities.each(function() {
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
                    let deliveredQuantity = document.getElementById(`deliveredQuantity-${index}`);
                    let cutBillQuantity = document.getElementById(`cutBillQuantity-${index}`);

                    let remainingAmount = numberFormat(quantity.value) - numberFormat(deliveredQuantity.value) - numberFormat(cutBillQuantity.value);
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
