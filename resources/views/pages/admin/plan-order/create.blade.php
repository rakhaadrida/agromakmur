@extends('layouts.admin')

@push('addon-style')
  <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/summernote/summernote-lite.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Plan Order</h1>
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
                            <form action="{{ route('plan-orders.store') }}" method="POST" id="form">
                                @csrf
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Order Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" tabindex="1" class="form-control form-control-sm text-bold" name="number" id="number" value="{{ old('number') }}" autofocus required >
                                                </div>
                                                <div class="col-1"></div>
                                                <label for="date" class="col-1 col-form-label text-bold text-right">Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" tabindex="2" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ $date }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col" style="margin-left: -320px">
                                            <div class="form-group row subtotal-po">
                                                <label for="subtotal" class="col-5 col-form-label text-bold ">Sub Total</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2">Rp</span>
                                                <div class="col-5">
                                                    <input type="text" readonly class="form-control-plaintext col-form-label-sm text-bold text-right" name="subtotal" id="subtotal">
                                                </div>
                                            </div>
                                            <div class="form-group row" style="margin-top: -25px">
                                                <label for="taxAmount" class="col-5 col-form-label text-bold ">Tax Amount</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2">Rp</span>
                                                <div class="col-5">
                                                    <input type="text" readonly class="form-control-plaintext col-form-label-sm text-bold text-right" name="tax_amount" id="taxAmount">
                                                </div>
                                            </div>
                                            <div class="form-group row" style="margin-top: -25px">
                                                <label for="grandTotal" class="col-5 col-form-label text-bold ">Grand Total</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2">Rp</span>
                                                <div class="col-5">
                                                    <input type="text" readonly class="form-control-plaintext text-bold text-right text-danger" name="grand_total" id="grandTotal">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so" style="margin-top: -68px">
                                        <label for="branch" class="col-2 col-form-label text-bold text-right">Branch</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <select class="selectpicker branch-select-picker" name="branch_id" id="branch" data-live-search="true" data-size="6" title="Enter or Choose Branch Name" tabindex="5" required>
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}" data-tokens="{{ $branch->name }}" @if($branches->count() == 1) selected @endif>{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('branch')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="supplier" class="col-2 col-form-label text-bold text-right">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <select class="selectpicker supplier-select-picker" name="supplier_id" id="supplier" data-live-search="true" data-size="6" title="Enter or Choose Supplier Name" tabindex="5" required>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}" data-tokens="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('supplier')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row po-note">
                                        <label for="note" class="col-2 col-form-label text-bold text-right">Note</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-6">
                                            <textarea class="form-control form-control-sm mt-1 text-dark" name="note" id="note"></textarea>
{{--                                            <input type="text" class="form-control form-control-sm mt-1 text-dark" name="note" id="note">--}}
                                        </div>
                                        <input type="hidden" name="row_number" id="rowNumber" value="{{ $rowNumbers }}">
                                    </div>
                                </div>
                                <hr>
                                <span class="float-right mb-3 mr-2" id="addRow"><a href="#" class="text-primary text-bold">
                                    Add Row <i class="fas fa-plus fa-lg ml-2" aria-hidden="true"></i></a>
                                </span>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td class="align-middle table-head-number-transaction">No</td>
                                            <td class="align-middle table-head-code-transaction">SKU</td>
                                            <td class="align-middle table-head-name-transaction">Product Name</td>
                                            <td class="align-middle table-head-quantity-transaction">Qty</td>
                                            <td class="align-middle table-head-unit-transaction">Unit</td>
                                            <td class="align-middle table-head-price-transaction">Price</td>
                                            <td class="align-middle table-head-total-transaction">Total</td>
                                            <td class="align-middle table-head-delete-transaction">Delete</td>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTable">
                                        @foreach($rows as $key => $row)
                                            <tr class="text-bold text-dark" id="{{ $key }}">
                                                <td class="align-middle text-center">{{ $row }}</td>
                                                <td>
                                                    <select class="selectpicker product-sku-plan-select-picker" name="product_id[]" id="productId-{{ $key }}" data-live-search="true" data-size="6" title="Enter Product SKU" tabindex="{{ $rowNumbers += 1 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $key }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-name-plan-select-picker" name="product_name[]" id="productName-{{ $key }}" data-live-search="true" data-size="6" title="Or Product Name..." tabindex="{{ $rowNumbers += 2 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->name }}">{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="quantity[]" id="quantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('quantity[]') }}" tabindex="{{ $rowNumbers += 3 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-unit-plan-select-picker" name="unit[]" id="unit-{{ $key }}" data-live-search="true" data-size="6" title="" tabindex="{{ $rowNumbers += 4 }}" disabled @if($key == 0) required @endif>
                                                    </select>
                                                    <input type="hidden" name="unit_id[]" id="unitValue-{{ $key }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="price[]" id="price-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('price[]') }}" tabindex="{{ $rowNumbers += 5 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="total[]" id="total-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('total[]') }}" title="" readonly >
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
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                         <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit" tabindex="10000">Submit</button>
                                    </div>
                                    <div class="col-2">
                                        <button type="reset" class="btn btn-outline-danger btn-block text-bold" id="btnReset" tabindex="10001">Reset</button>
                                    </div>
                                </div>

                                <div class="modal" id="modalConfirmation" tabindex="-1" role="dialog" aria-labelledby="modalConfirmation" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" class="h2 text-bold">&times;</span>
                                                </button>
                                                <h4 class="modal-title">Goods Receipt Confirmation</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>The Goods Receipt data will be saved. Please select print or re-enter the incoming goods.</p>
                                                <input type="hidden" name="is_print" value="0">
                                                <hr>
                                                <div class="form-row justify-content-center">
                                                    <div class="col-3">
                                                        <button type="button" class="btn btn-success btn-block text-bold" id="btnPrint">Print</button>
                                                    </div>
                                                    <div class="col-4">
                                                        <button type="submit" class="btn btn-outline-secondary btn-block text-bold">Input Another</button>
                                                    </div>
                                                </div>
                                            </div>
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
    <script src="{{ url('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ url('assets/vendor/summernote/summernote-lite.js') }}"></script>
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
            const note = $('#note');
            note.summernote({
                height: 60,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']]
                ]
            });

            note.summernote('fontSize', 14);

            $('.note-btn-bold').removeClass('active').style('font-size', '12px');

            const table = $('#itemTable');
            let subtotal = document.getElementById('subtotal');

            table.on('change', 'select[name="product_id[]"]', function () {
                const index = $(this).closest('tr').index();
                displayPrice(this.value, index, false);
            });

            table.on('change', 'select[name="product_name[]"]', function () {
                const index = $(this).closest('tr').index();
                displayPrice(this.value, index, true);
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

                    $('#modalConfirmation').modal('show');

                    return false;
                }
            });

            $('#btnPrint').on('click', function(event) {
                event.preventDefault();

                $('input[name="is_print"]').val(1);
                $('#form').submit();
            });

            $('#addRow').on('click', function(event) {
                event.preventDefault();

                let itemTable = $('#itemTable');
                let lastRowId = itemTable.find('tr:last').attr('id');
                let lastRowNumber = itemTable.find('tr:last td:first-child').text();
                let rowNumbers = $('#rowNumber').val();
                rowNumbers = +rowNumbers + (+lastRowNumber * 17);

                let rowId = lastRowId ? +lastRowId + 1 : 1;
                let rowNumber = lastRowNumber ? +lastRowNumber + 1 : 1;
                let newRow = newRowElement(rowId, rowNumber, rowNumbers);

                itemTable.append(newRow);

                $(`#productId-${rowId}`).selectpicker();
                $(`#productName-${rowId}`).selectpicker();
            });

            function displayPrice(productId, index, isProductName) {
                $.ajax({
                    url: '{{ route('goods-receipts.index-ajax') }}',
                    type: 'GET',
                    data: {
                        supplier_id: $('#supplier').val(),
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(data) {
                        let productName = $(`#productName-${index}`);
                        if(isProductName) {
                            productName = $(`#productId-${index}`);
                        }

                        let productSku = $(`#productId-${index} option[value="${data.data.id}"]`);
                        let price = $(`#price-${index}`);
                        let quantity = $(`#quantity-${index}`);
                        let supplierId = $('#supplier').val();

                        let productPrice = thousandSeparator(data.main_price);
                        productSku.attr(`data-supplier-${supplierId}`, data.main_price);
                        let productUnitId = data.data.unit_id;

                        productName.selectpicker('val', productId);
                        price.val(productPrice);
                        price.attr('readonly', false);
                        price.attr('required', true);
                        quantity.attr('readonly', false);
                        quantity.attr('required', true);

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

            function calculateTotal(index) {
                let quantity = document.getElementById(`quantity-${index}`);
                let price = document.getElementById(`price-${index}`);
                let total = document.getElementById(`total-${index}`);

                let realQuantity = getRealQuantity(numberFormat(quantity.value), index);
                let currentTotal = 0;

                if(quantity.value === "") {
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) - numberFormat(total.value));
                    total.value = '';
                }
                else {
                    currentTotal = numberFormat(total.value);
                    total.value = thousandSeparator(realQuantity * numberFormat(price.value));
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

                let taxValue = (subtotalAmount * 0.1).toFixed(0);

                taxAmount.value = thousandSeparator(taxValue);
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
                    let total = document.getElementById(`total-${i}`);
                    let realQuantity = document.getElementById(`realQuantity-${i}`);
                    let unitValue = document.getElementById(`unitValue-${i}`);

                    let rowNumber = +i + 1;
                    let newProductId = document.getElementById(`productId-${rowNumber}`);
                    let newProductName = document.getElementById(`productId-${rowNumber}`);
                    let newQuantity = document.getElementById(`quantity-${rowNumber}`);
                    let newPrice = document.getElementById(`price-${rowNumber}`);
                    let newUnit = document.getElementById(`unit-${rowNumber}`);
                    let newTotal = document.getElementById(`total-${rowNumber}`);
                    let newRealQuantity = document.getElementById(`realQuantity-${rowNumber}`);
                    let newUnitValue = document.getElementById(`unitValue-${rowNumber}`);

                    if(rowNumber !== deleteRow.length) {
                        total.value = newTotal.value;
                        price.value = newPrice.value;
                        quantity.value = newQuantity.value;
                        realQuantity.value = newRealQuantity.value;
                        unitValue.value = newUnitValue.value;

                        changeSelectPickerValue($(`#unit-${i}`), newUnit.value, rowNumber, true);
                        changeSelectPickerValue($(`#productName-${i}`), newProductName.value, rowNumber, false);
                        changeSelectPickerValue($(`#productId-${i}`), newProductId.value, rowNumber, false);

                        if(newProductId.value === '') {
                            handleDeletedQuantityPrice(quantity, price);
                            updateDeletedRowValue([], i);
                        } else {
                            newQuantity.removeAttribute('required');
                            newPrice.removeAttribute('required');
                            quantity.removeAttribute('readonly');
                            price.removeAttribute('readonly');
                        }

                        let elements = [newTotal, newPrice, newQuantity, newRealQuantity, newUnitValue];
                        updateDeletedRowValue(elements, rowNumber);
                    } else {
                        let totalRow = $('#rowNumber').val();
                        if(rowNumber > totalRow) {
                            $(`#${i}`).remove();
                        }

                        handleDeletedQuantityPrice(quantity, price);

                        let elements = [total, price, quantity, realQuantity, unitValue];
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

            function handleDeletedQuantityPrice(quantity, price) {
                quantity.removeAttribute('required');
                price.removeAttribute('required');
                quantity.readOnly = true;
                price.readOnly = true;
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
                            <select class="selectpicker product-sku-plan-select-picker" name="product_id[]" id="productId-${rowId}" data-live-search="true" data-size="6" title="Enter Product SKU" tabindex="${rowNumbers += 1}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="real_quantity[]" id="realQuantity-${rowId}">
                        </td>
                        <td>
                            <select class="selectpicker product-name-plan-select-picker" name="product_name[]" id="productName-${rowId}" data-live-search="true" data-size="6" title="Or Product Name..." tabindex="${rowNumbers += 2}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->name }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="quantity[]" id="quantity-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('quantity[]') }}" tabindex="${rowNumbers += 3}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
                        </td>
                        <td>
                            <select class="selectpicker product-unit-plan-select-picker" name="unit[]" id="unit-${rowId}" data-live-search="true" data-size="6" title="" tabindex="${rowNumbers += 4}" disabled>
                            </select>
                            <input type="hidden" name="unit_id[]" id="unitValue-${rowId}">
                        </td>
                        <td>
                            <input type="text" name="price[]" id="price-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('price[]') }}" tabindex="${rowNumbers += 5}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
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
