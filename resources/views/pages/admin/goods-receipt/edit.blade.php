@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Edit Goods Receipt</h1>
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
                            <form action="{{ route('goods-receipts.update', $goodsReceipt->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="container so-container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-dark text-right">Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark" name="number" id="number" value="{{ $goodsReceipt->number }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col edit-receipt-general-info-right">
                                            <div class="form-group row sj-first-line">
                                                <label for="warehouse" class="col-5 col-form-label text-bold text-right text-dark">Warehouse</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-5">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="warehouse" id="warehouse" value="{{ $goodsReceipt->warehouse->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row sj-after-first">
                                                <label for="supplier" class="col-5 col-form-label text-bold text-right text-dark">Supplier</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="supplier" id="supplier" value="{{ $goodsReceipt->supplier->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row sj-after-first">
                                                <label for="dueDate" class="col-5 col-form-label text-bold text-right text-dark">Due Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-5">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="due_date" id="dueDate" value="{{ getDueDate($goodsReceipt->date, $goodsReceipt->tempo, 'd-m-Y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row so-update-left">
                                        <label for="date" class="col-2 col-form-label text-bold text-dark text-right">Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark" name="date" id="date" value="{{ formatDate($goodsReceipt->date, 'd-m-Y') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row gr-update-input">
                                        <label for="description" class="col-2 col-form-label text-bold text-dark text-right">Description</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm mt-1 text-dark" name="description" id="description" tabindex="1" required autofocus>
                                            <input type="hidden" name="row_number" id="rowNumber" value="{{ $rowNumbers }}">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <span class="float-right mb-3 mr-2" id="addRow"><a href="#" class="text-primary text-bold">
                                    Add Row <i class="fas fa-plus fa-lg ml-2" aria-hidden="true"></i></a>
                                </span>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" >
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td class="align-middle table-head-number-transaction">No</td>
                                            <td class="align-middle table-head-code-transaction">SKU</td>
                                            <td class="align-middle table-head-name-transaction">Product Name</td>
                                            <td class="align-middle table-head-quantity-transaction">Qty</td>
                                            <td class="align-middle table-head-unit-transaction">Unit</td>
                                            <td class="align-middle table-head-price-transaction">Price</td>
                                            <td class="align-middle table-head-price-transaction">Wages</td>
                                            <td class="align-middle table-head-price-transaction">Shipping Cost</td>
                                            <td class="align-middle table-head-total-transaction">Total</td>
                                            <td class="align-middle table-head-delete-transaction">Delete</td>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTable">
                                        @foreach($goodsReceiptItems as $key => $goodsReceiptItem)
                                            <tr class="text-bold text-dark" id="{{ $key }}">
                                                <td class="align-middle text-center">{{ $key + 1 }}</td>
                                                <td>
                                                    <select class="selectpicker product-sku-select-picker" name="product_id[]" id="productId-{{ $key }}" data-live-search="true" title="Enter SKU" tabindex="{{ $rowNumbers += 1 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}" @if($goodsReceiptItem->product_id == $product->id) selected @endif>{{ $product->sku }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $key }}" value="{{ getRealQuantity($goodsReceiptItem->quantity, $goodsReceiptItem->actual_quantity) }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-name-select-picker" name="product_name[]" id="productName-{{ $key }}" data-live-search="true" title="Or Product Name..." tabindex="{{ $rowNumbers += 2 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->name }}" @if($goodsReceiptItem->product_id == $product->id) selected @endif>{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="quantity[]" id="quantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatQuantity($goodsReceiptItem->quantity) }}" tabindex="{{ $rowNumbers += 3 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-unit-select-picker" name="unit[]" id="unit-{{ $key }}" data-live-search="true" title="" tabindex="{{ $rowNumbers += 4 }}" @if($key == 0) required @endif>
                                                        @foreach($units[$goodsReceiptItem->product_id] as $unit)
                                                            <option value="{{ $unit['id'] }}" data-tokens="{{ $unit['name'] }}" data-foo="{{ $unit['quantity'] }}" @if($goodsReceiptItem->unit_id == $unit['id']) selected @endif>{{ $unit['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="unit_id[]" id="unitValue-{{ $key }}" value="{{ $goodsReceiptItem->unit_id }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="price[]" id="price-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatPrice($goodsReceiptItem->price) }}" tabindex="{{ $rowNumbers += 5 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="wages[]" id="wages-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatPrice($goodsReceiptItem->wages) }}" tabindex="{{ $rowNumbers += 6 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers">
                                                </td>
                                                <td>
                                                    <input type="text" name="shipping_cost[]" id="shippingCost-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatPrice($goodsReceiptItem->shipping_cost) }}" tabindex="{{ $rowNumbers += 7 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers">
                                                </td>
                                                <td>
                                                    <input type="text" name="total[]" id="total-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ formatPrice($goodsReceiptItem->total) }}" title="" readonly >
                                                </td>
                                                <td class="align-middle text-center">
                                                    <button type="button" class="remove-transaction-table" id="deleteRow[]">
                                                        <i class="fas fa-fw fa-times fa-lg ic-remove mt-1"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="form-group row justify-content-end subtotal-so">
                                    <label for="subtotal" class="col-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-2 mr-1">
                                        <input type="text" id="subtotal" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($goodsReceipt->subtotal) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so">
                                    <label for="taxAmount" class="col-2 col-form-label text-bold text-right text-dark">Tax Amount</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-2 mr-1">
                                        <input type="text" id="taxAmount" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($goodsReceipt->tax_amount) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end grandtotal-so">
                                    <label for="grandTotal" class="col-2 col-form-label text-bold text-right text-dark">Grand Total</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-2 mr-1">
                                        <input type="text" id="grandTotal" class="form-control-plaintext text-bold text-danger text-right text-lg" value="{{ formatPrice($goodsReceipt->grand_total) }}" readonly>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit" tabindex="10000">Submit</button>
                                    </div>
                                    <div class="col-2">
                                        <button type="reset" class="btn btn-outline-secondary btn-block text-bold" tabindex="10001">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalDuplicate" tabindex="-1" role="dialog" aria-labelledby="modalDuplicate" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="h2 text-bold">&times;</span>
                    </button>
                    <h4 class="modal-title text-bold">Product Notification</h4>
                </div>
                <div class="modal-body text-dark">
                    <h5>There are identical item codes such as - (<span class="text-bold" id="duplicateCode"></span>). Please add up the quantities of the same item codes or change the item code.</h5>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            const table = $('#itemTable');
            let subtotal = document.getElementById('subtotal');

            table.on('change', 'select[name="product_id[]"]', function () {
                const index = $(this).closest('tr').index();
                displayPrice(this.value, index);
            });

            table.on('keypress', 'input[name="quantity[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();
                    $(`#quantity-${index}`).tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="quantity[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            table.on('blur', 'input[name="quantity[]"]', function () {
                const index = $(this).closest('tr').index();
                calculateTotal(index);
            });

            table.on('change', 'select[name="unit[]"]', function () {
                const index = $(this).closest('tr').index();
                const selected = $(this).find(':selected');

                $(`#unitValue-${index}`).val(this.value);
                $(`#realQuantity-${index}`).val(selected.data('foo'));

                calculateTotal(index);
            });

            table.on('keypress', 'input[name="price[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();
                    $(`#price-${index}`).tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="price[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            table.on('blur', 'input[name="price[]"]', function () {
                const index = $(this).closest('tr').index();
                calculateTotal(index);
            });

            table.on('keypress', 'input[name="wages[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();
                    $(`#wages-${index}`).tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="wages[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            table.on('blur', 'input[name="wages[]"]', function () {
                const index = $(this).closest('tr').index();
                calculateTotal(index);
            });

            table.on('keypress', 'input[name="shipping_cost[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();
                    $(`#shippingCost-${index}`).tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="shipping_cost[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            table.on('blur', 'input[name="shipping_cost[]"]', function () {
                const index = $(this).closest('tr').index();
                calculateTotal(index);
            });

            table.on('click', '.remove-transaction-table', function () {
                const index = $(this).closest('tr').index();
                const deleteRow = $('.remove-transaction-table');

                updateAllRowIndexes(index, deleteRow);
            });

            $('#btnSubmit').on('click', function(event) {
                event.preventDefault();

                let checkForm = document.getElementById('form').checkValidity();
                if(!checkForm) {
                    document.getElementById('form').reportValidity();
                    return false;
                }

                let duplicateCodes = checkDuplicateProduct();
                if(duplicateCodes.length) {
                    let duplicateCode = duplicateCodes.join(', ');

                    $('#duplicateCode').text(duplicateCode);
                    $('#modalDuplicate').modal('show');

                    return false;
                } else {
                    $('input[name="quantity[]"]').each(function() {
                        this.value = numberFormat(this.value);
                    });

                    $('input[name="price[]"]').each(function() {
                        this.value = numberFormat(this.value);
                    });

                    $('input[name="wages[]"]').each(function() {
                        this.value = numberFormat(this.value);
                    });

                    $('input[name="shipping_cost[]"]').each(function() {
                        this.value = numberFormat(this.value);
                    });

                    $('#form').submit();
                }
            });

            $('#addRow').on('click', function(event) {
                event.preventDefault();

                let itemTable = $('#itemTable');
                let lastRowId = itemTable.find('tr:last').attr('id');
                let lastRowNumber = itemTable.find('tr:last td:first-child').text();
                let rowNumbers = $('#rowNumber').val();
                rowNumbers = +rowNumbers + (+lastRowNumber * 32);

                let rowId = lastRowId ? +lastRowId + 1 : 1;
                let rowNumber = lastRowNumber ? +lastRowNumber + 1 : 1;
                let newRow = newRowElement(rowId, rowNumber, rowNumbers);

                itemTable.append(newRow);

                $(`#productId-${rowId}`).selectpicker();
                $(`#productName-${rowId}`).selectpicker();
            });

            function displayPrice(productId, index) {
                $.ajax({
                    url: '{{ route('products.index-ajax') }}',
                    type: 'GET',
                    data: {
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(data) {
                        let productName = $(`#productName-${index}`);
                        let price = $(`#price-${index}`);
                        let wages = $(`#wages-${index}`);
                        let shippingCost = $(`#shippingCost-${index}`);
                        let quantity = $(`#quantity-${index}`);
                        let productPrice = thousandSeparator(data.main_price);
                        let productUnitId = data.data.unit_id;

                        productName.selectpicker('val', productId);
                        price.val(productPrice);

                        let elements = [wages, shippingCost, price, quantity];
                        handleRequiredReadonlyAttribute(elements);

                        let units = data.units;
                        let unit = $(`#unit-${index}`);
                        unit.empty();

                        $.each(units, function(key, item) {
                            unit.append(
                                $('<option></option>', {
                                    value: item.id,
                                    text: item.name,
                                    'data-tokens': item.name,
                                    'data-foo': item.quantity,
                                })
                            );

                            unit.attr('disabled', false);
                            unit.selectpicker('refresh');
                            unit.selectpicker('val', productUnitId);
                            $(`#unitValue-${index}`).val(productUnitId);
                        });

                        $(`#realQuantity-${index}`).val(1);

                        calculateTotal(index);
                    },
                })
            }

            function handleRequiredReadonlyAttribute(elements) {
                elements.forEach(function(element) {
                    element.attr('readonly', false);
                    element.attr('required', true);
                });
            }

            function calculateTotal(index) {
                let quantity = document.getElementById(`quantity-${index}`);
                let price = document.getElementById(`price-${index}`);
                let wages = document.getElementById(`wages-${index}`);
                let shippingCost = document.getElementById(`shippingCost-${index}`);
                let total = document.getElementById(`total-${index}`);

                let realQuantity = getRealQuantity(numberFormat(quantity.value), index);
                let currentTotal = 0;

                if(quantity.value === "") {
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) - numberFormat(total.value));
                    total.value = '';
                }
                else {
                    let wagesAmount = numberFormat(wages.value) || 0;
                    let shippingCostAmount = numberFormat(shippingCost.value) || 0;
                    let totalExpenses = wagesAmount + shippingCostAmount;

                    currentTotal = numberFormat(total.value);
                    total.value = thousandSeparator(realQuantity * numberFormat(price.value) + totalExpenses);
                    calculateSubtotal(currentTotal, numberFormat(total.value), subtotal);
                }

                calculateTax(numberFormat(subtotal.value));
            }

            function getRealQuantity(quantity, index) {
                let realQuantity = $(`#realQuantity-${index}`).val();

                return +quantity * +realQuantity;
            }

            function calculateSubtotal(previousAmount, currentAmount, subtotal) {
                if(previousAmount > currentAmount) {
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) - (+previousAmount - +currentAmount));
                } else {
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) + (+currentAmount - +previousAmount));
                }
            }

            function calculateTax(subtotalAmount) {
                let taxAmount = document.getElementById('taxAmount');
                let grandTotal = document.getElementById('grandTotal');

                taxAmount.value = thousandSeparator(subtotalAmount * 0.1);
                grandTotal.value = thousandSeparator(subtotalAmount + numberFormat(taxAmount.value));
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

            function updateAllRowIndexes(index, deleteRow) {
                let quantity = document.getElementById(`quantity-${index}`);
                let total = document.getElementById(`total-${index}`);

                if(quantity.value !== '') {
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) - numberFormat(total.value));
                    calculateTax(numberFormat(subtotal.value));
                }

                for(let i = index; i < deleteRow.length; i++) {
                    let quantity = document.getElementById(`quantity-${i}`);
                    let price = document.getElementById(`price-${i}`);
                    let wages = document.getElementById(`wages-${i}`);
                    let shippingCost = document.getElementById(`shippingCost-${i}`);
                    let total = document.getElementById(`total-${i}`);
                    let realQuantity = document.getElementById(`realQuantity-${i}`);
                    let unitValue = document.getElementById(`unitValue-${i}`);

                    let rowNumber = +i + 1;
                    let newProductId = document.getElementById(`productId-${rowNumber}`);
                    let newProductName = document.getElementById(`productId-${rowNumber}`);
                    let newQuantity = document.getElementById(`quantity-${rowNumber}`);
                    let newPrice = document.getElementById(`price-${rowNumber}`);
                    let newWages = document.getElementById(`wages-${rowNumber}`);
                    let newShippingCost = document.getElementById(`shippingCost-${rowNumber}`);
                    let newUnit = document.getElementById(`unit-${rowNumber}`);
                    let newTotal = document.getElementById(`total-${rowNumber}`);
                    let newRealQuantity = document.getElementById(`realQuantity-${rowNumber}`);
                    let newUnitValue = document.getElementById(`unitValue-${rowNumber}`);

                    if(rowNumber !== deleteRow.length) {
                        total.value = newTotal.value;
                        price.value = newPrice.value;
                        wages.value = newWages.value;
                        shippingCost.value = newShippingCost.value;
                        quantity.value = newQuantity.value;
                        realQuantity.value = newRealQuantity.value;
                        unitValue.value = newUnitValue.value;

                        changeSelectPickerValue($(`#unit-${i}`), newUnit.value, rowNumber, true);
                        changeSelectPickerValue($(`#productName-${i}`), newProductName.value, rowNumber, false);
                        changeSelectPickerValue($(`#productId-${i}`), newProductId.value, rowNumber, false);

                        if(newProductId.value === '') {
                            let deletedElements = [quantity, price, wages, shippingCost];
                            handleDeletedElementAttribute(deletedElements);

                            updateDeletedRowValue([], i);
                        } else {
                            handleRemoveRequiredReadonly(newQuantity, quantity);
                            handleRemoveRequiredReadonly(newPrice, price);
                            handleRemoveRequiredReadonly(newWages, wages);
                            handleRemoveRequiredReadonly(newShippingCost, shippingCost);
                        }

                        let elements = [newTotal, newShippingCost, newWages, newPrice, newQuantity, newRealQuantity, newUnitValue];
                        updateDeletedRowValue(elements, rowNumber);
                    } else {
                        if(rowNumber > 1) {
                            $(`#${i}`).remove();
                        }

                        let deletedElements = [quantity, price, wages, shippingCost];
                        handleDeletedElementAttribute(deletedElements);

                        let elements = [total, shippingCost, wages, price, quantity, realQuantity, unitValue];
                        updateDeletedRowValue(elements, i);
                    }
                }
            }

            function changeSelectPickerValue(selectElement, value, index, isRemoveDisabled) {
                if(isRemoveDisabled) {
                    let newUnit = $(`#unit-${index}`);

                    if(!newUnit.is(':disabled')) {
                        selectElement.empty();
                        selectElement.append(newUnit.html()).find('option');
                        selectElement.selectpicker('refresh');
                        selectElement.attr('disabled', false);
                    }
                }

                selectElement.selectpicker('val', value);
                selectElement.selectpicker('refresh');
            }

            function removeSelectPickerOption(selectElement, isDisabled) {
                selectElement.selectpicker('val', '');

                if(isDisabled) {
                    selectElement.find('option').remove();
                    selectElement.attr('disabled', true);
                }

                selectElement.selectpicker('refresh');
            }

            function updateDeletedRowValue(elements, index) {
                elements.forEach(function(element) {
                    element.value = '';
                });

                removeSelectPickerOption($(`#unit-${index}`), true);
                removeSelectPickerOption($(`#productName-${index}`), false);
                removeSelectPickerOption($(`#productId-${index}`), false);
            }

            function handleDeletedElementAttribute(elements) {
                elements.forEach(function(element) {
                    element.removeAttribute('required');
                    element.readOnly = true;
                });
            }

            function handleRemoveRequiredReadonly(newElement, element) {
                newElement.removeAttribute('required');
                element.removeAttribute('readonly');
            }

            function checkDuplicateProduct() {
                let productIdElements = $('select[name="product_id[]"]');

                let productIds = [];
                let productDuplicates = [];

                productIdElements.each(function() {
                    let productId = $(this).val();
                    let productSku = $(this).find(':selected').data('tokens');
                    if(productId) {
                        if(productIds.includes(productId)) {
                            productDuplicates.push(productSku);
                        } else {
                            productIds.push(productId);
                        }
                    }
                });

                return [...new Set(productDuplicates)];
            }

            function newRowElement(rowId, rowNumber, rowNumbers) {
                return `
                    <tr class="text-bold text-dark" id="${rowId}">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td>
                            <select class="selectpicker product-sku-select-picker" name="product_id[]" id="productId-${rowId}" data-live-search="true" title="Enter SKU" tabindex="${rowNumbers += 1}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="real_quantity[]" id="realQuantity-${rowId}">
                        </td>
                        <td>
                            <select class="selectpicker product-name-select-picker" name="product_name[]" id="productName-${rowId}" data-live-search="true" title="Or Product Name..." tabindex="${rowNumbers += 2}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->name }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="quantity[]" id="quantity-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('quantity[]') }}" tabindex="${rowNumbers += 3}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
                        </td>
                        <td>
                            <select class="selectpicker product-unit-select-picker" name="unit[]" id="unit-${rowId}" data-live-search="true" title="" tabindex="${rowNumbers += 4}" disabled>
                            </select>
                            <input type="hidden" name="unit_id[]" id="unitValue-${rowId}">
                        </td>
                        <td>
                            <input type="text" name="price[]" id="price-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('price[]') }}" tabindex="${rowNumbers += 5}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
                        </td>
                        <td>
                            <input type="text" name="wages[]" id="wages-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('wages[]') }}" tabindex="${rowNumbers += 6}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
                        </td>
                        <td>
                            <input type="text" name="shipping_cost[]" id="shippingCost-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('shipping_cost[]') }}" tabindex="${rowNumbers += 7}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
                        </td>
                        <td>
                            <input type="text" name="total[]" id="total-${rowId}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('total[]') }}" title="" readonly >
                        </td>
                        <td class="align-middle text-center">
                            <button type="button" class="remove-transaction-table" id="deleteRow[]">
                                <i class="fas fa-fw fa-times fa-lg ic-remove mt-1"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }
        });
    </script>
@endpush
