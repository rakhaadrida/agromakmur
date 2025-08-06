@extends('layouts.admin')

@push('addon-style')
  <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Sales Order</h1>
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
                            <form action="{{ route('sales-orders.store') }}" method="POST" id="form">
                                @csrf
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Order Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold" name="number" id="number" value="{{ old('number') }}" tabindex="1" autofocus required >
                                                </div>
                                                <label for="date" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Order Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ $date }}" tabindex="2" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col sales-order-right-input">
                                            <div class="form-group row ">
                                                <label for="tempo" class="col-5 col-form-label text-bold text-right">Tempo</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-3 mt-1">
                                                    <input type="text" name="tempo" id="tempo" class="form-control form-control-sm text-bold" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" tabindex="4">
                                                </div>
                                                <span class="col-form-label text-bold"> Day(s)</span>
                                            </div>
                                            <div class="form-group row sales-order-is-taxable-input">
                                                <label for="taxAmount" class="col-5 col-form-label text-bold text-right">Is Taxable</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2"></span>
                                                <div class="col-3 pkp-check">
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="radio" name="is_taxable" id="isTaxableYes" value="1" tabindex="5">
                                                        <label class="form-check-label text-bold text-dark" for="isTaxableYes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="is_taxable" id="isTaxableNo" value="0" tabindex="5">
                                                        <label class="form-check-label text-bold text-dark" for="isTaxableNo">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row sales-order-customer-input">
                                        <label for="customer" class="col-2 col-form-label text-bold text-right">Customer</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <select class="selectpicker warehouse-select-picker" name="customer_id" id="customer" data-live-search="true" title="Enter or Choose Customer" tabindex="3" required>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" data-tokens="{{ $customer->name }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('warehouse')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <label for="taxNumber" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Tax Number</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" name="tax_number" id="taxNumber" class="form-control form-control-sm text-bold" tabindex="4" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="marketing" class="col-2 col-form-label text-bold text-right">Marketing</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <select class="selectpicker marketing-select-picker" name="marketing_id" id="marketing" data-live-search="true" title="Enter or Choose Marketing" tabindex="5" required>
                                                @foreach($marketings as $marketing)
                                                    <option value="{{ $marketing->id }}" data-tokens="{{ $marketing->name }}">{{ $marketing->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('supplier')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <label for="date" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Delivery Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ $date }}" tabindex="2" required>
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
                                            <td rowspan="2" class="align-middle table-head-number-sales-order">No</td>
                                            <td rowspan="2" class="align-middle table-head-code-sales-order">SKU</td>
                                            <td rowspan="2" class="align-middle">Product Name</td>
                                            <td rowspan="2" class="align-middle table-head-quantity-sales-order">Qty</td>
                                            <td rowspan="2" class="align-middle table-head-unit-sales-order">Unit</td>
                                            <td rowspan="2" class="align-middle table-head-price-type-sales-order">Price Type</td>
                                            <td rowspan="2" class="align-middle table-head-price-sales-order">Price</td>
                                            <td rowspan="2" class="align-middle table-head-total-sales-order">Total</td>
                                            <td colspan="2" class="align-middle">Discount</td>
                                            <td rowspan="2" class="align-middle table-head-final-amount-sales-order">Final Amount</td>
                                            <td rowspan="2" class="align-middle table-head-delete-transaction">Delete</td>
                                        </tr>
                                        <tr>
                                            <td class="table-head-discount-percentage-sales-order">%</td>
                                            <td class="table-head-discount-amount-sales-order">Rupiah</td>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTable">
                                        @foreach($rows as $key => $row)
                                            <tr class="text-bold text-dark" id="{{ $key }}">
                                                <td class="align-middle text-center">{{ $row }}</td>
                                                <td>
                                                    <select class="selectpicker sales-order-sku-select-picker" name="product_id[]" id="productId-{{ $key }}" data-live-search="true" title="Enter SKU" tabindex="{{ $rowNumbers += 1 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $key }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker sales-order-name-select-picker" name="product_name[]" id="productName-{{ $key }}" data-live-search="true" title="Or Product Name..." tabindex="{{ $rowNumbers += 2 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->name }}">{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="quantity[]" id="quantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('quantity[]') }}" tabindex="{{ $rowNumbers += 3 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <select class="selectpicker sales-order-unit-select-picker" name="unit[]" id="unit-{{ $key }}" data-live-search="true" title="" tabindex="{{ $rowNumbers += 4 }}" disabled @if($key == 0) required @endif>
                                                    </select>
                                                    <input type="hidden" name="unit_id[]" id="unitValue-{{ $key }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker sales-order-price-type-select-picker" name="price_type[]" id="priceType-{{ $key }}" data-live-search="true" title="" tabindex="{{ $rowNumbers += 5 }}" disabled @if($key == 0) required @endif>
                                                    </select>
                                                    <input type="hidden" name="price_id[]" id="priceId-{{ $key }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="price[]" id="price-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('price[]') }}" tabindex="{{ $rowNumbers += 6 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="total[]" id="total-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('total[]') }}" title="" readonly >
                                                </td>
                                                <td>
                                                    <input type="text" name="discount[]" id="discount-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('discount[]') }}" tabindex="{{ $rowNumbers += 7 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers and plus sign" readonly @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="discount_amount[]" id="discountAmount-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('discount_amount[]') }}" title="" readonly >
                                                </td>
                                                <td>
                                                    <input type="text" name="final_amount[]" id="finalAmount-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('final_amount[]') }}" title="" readonly >
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
                                <div class="form-group row justify-content-end subtotal-so sales-order-total-amount-info">
                                    <label for="totalAmount" class="col-3 col-form-label text-bold text-right text-dark">Total</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <span class="col-form-label text-bold ml-2">Rp</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control-plaintext form-control-sm text-bold text-secondary text-right mt-1" name="total_amount" id="totalAmount" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so sales-order-total-amount-info">
                                    <label for="invoiceDiscount" class="col-3 col-form-label text-bold text-right text-dark">Invoice Discount</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <span class="col-form-label text-bold ml-2">Rp</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right mt-1 invoice-discount" name="invoice_discount" id="invoiceDiscount" placeholder="Enter Discount">
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so sales-order-total-amount-info">
                                    <label for="subtotal" class="col-3 col-form-label text-bold text-right text-dark">Sub Total</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <span class="col-form-label text-bold ml-2">Rp</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control-plaintext form-control-sm text-bold text-secondary text-right mt-1" name="subtotal" id="subtotal" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so sales-order-total-amount-info">
                                    <label for="taxAmount" class="col-3 col-form-label text-bold text-right text-dark">Tax Amount</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <span class="col-form-label text-bold ml-2">Rp</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control-plaintext form-control-sm text-bold text-danger text-right" name="tax_amount" id="taxAmount" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so sales-order-total-amount-info">
                                    <label for="grandTotal" class="col-3 col-form-label text-bold text-right text-dark">Grand Total</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <span class="col-form-label text-bold ml-2">Rp</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control-plaintext form-control-sm text-bold text-danger text-right mt-1" name="grand_total" id="grandTotal" readonly>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                         <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit" tabindex="{{ $rowNumbers++ }}">Submit</button>
                                    </div>
                                    <div class="col-2">
                                        <button type="reset" class="btn btn-outline-danger btn-block text-bold" id="btnReset" tabindex="{{ $rowNumbers++ }}">Reset</button>
                                    </div>
                                </div>

                                <div class="modal" id="modalConfirmation" tabindex="-1" role="dialog" aria-labelledby="modalConfirmation" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" class="h2 text-bold">&times;</span>
                                                </button>
                                                <h4 class="modal-title">Sales Order Confirmation</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>The Sales Order data will be saved. Please select print or re-enter the sales order.</p>
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
            let totalAmount = document.getElementById('totalAmount');
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

            table.on('change', 'select[name="price_type[]"]', function () {
                const index = $(this).closest('tr').index();
                const selected = $(this).find(':selected');

                $(`#priceId-${index}`).val(this.value);
                $(`#price-${index}`).val(thousandSeparator(selected.data('foo')));

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

            table.on('keypress', 'input[name="discount[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && event.which !== 43 && event.which !== 44 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();
                    $(`#discount-${index}`).tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('blur', 'input[name="discount[]"]', function () {
                const index = $(this).closest('tr').index();
                calculateDiscount(index);
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

                $('input[name="quantity[]"]').each(function() {
                    this.value = numberFormat(this.value);
                });

                $('input[name="price[]"]').each(function() {
                    this.value = numberFormat(this.value);
                });

                let duplicateCodes = checkDuplicateProduct();
                if(duplicateCodes.length) {
                    let duplicateCode = duplicateCodes.join(', ');

                    $('#duplicateCode').text(duplicateCode);
                    $('#modalDuplicate').modal('show');

                    return false;
                } else {
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

                let rowId = lastRowId ? +lastRowId + 1 : 1;
                let rowNumber = lastRowNumber ? +lastRowNumber + 1 : 1;
                let newRow = newRowElement(rowId, rowNumber);

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
                        let discount = $(`#discount-${index}`);
                        let quantity = $(`#quantity-${index}`);
                        let productPrice = thousandSeparator(data.main_price);
                        let productUnitId = data.data.unit_id;
                        let productPriceId = data.data.main_price_id;

                        productName.selectpicker('val', productId);
                        price.val(productPrice);
                        changeReadonlyRequired(price);
                        changeReadonlyRequired(discount);
                        changeReadonlyRequired(quantity);

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

                        let priceTypes = data.prices;
                        let priceType = $(`#priceType-${index}`);
                        priceType.empty();

                        $.each(priceTypes, function(key, item) {
                            priceType.append(
                                $('<option></option>', {
                                    value: item.id,
                                    text: item.code,
                                    'data-tokens': item.code,
                                    'data-foo': item.price,
                                })
                            );

                            priceType.attr('disabled', false);
                            priceType.selectpicker('refresh');
                            priceType.selectpicker('val', productPriceId);
                            $(`#priceId-${index}`).val(productPriceId);
                        });

                        $(`#realQuantity-${index}`).val(1);

                        calculateTotal(index);
                    },
                })
            }

            function calculateDiscount(index) {
                let discount = document.getElementById(`discount-${index}`);
                let discountAmount = document.getElementById(`discountAmount-${index}`);
                let finalAmount = document.getElementById(`finalAmount-${index}`);
                let total = document.getElementById(`total-${index}`);

                if(discount.value === '') {
                    totalAmount.value = thousandSeparator(numberFormat(totalAmount.value) + numberFormat(discountAmount.value));
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) + numberFormat(discountAmount.value));
                    discountAmount.value = '';
                    finalAmount.value = total.value;
                } else {
                    let currentFinalAmount = numberFormat(finalAmount.value);
                    let discountPercentage = calculateDiscountPercentage(discount.value);
                    let totalValue = numberFormat(total.value);
                    let discountValue = ((discountPercentage * totalValue) / 100).toFixed(0);

                    discountAmount.value = thousandSeparator(discountValue);
                    finalAmount.value = thousandSeparator(totalValue - discountValue);

                    calculateSubtotal(currentFinalAmount, numberFormat(finalAmount.value), subtotal, totalAmount);
                }

                calculateTax(numberFormat(subtotal.value));
            }

            function calculateDiscountPercentage(value) {
                let maxDiscount = 100;

                value.replace(/\,/g, ".");
                let arrayDiscount = value.split('+');

                arrayDiscount.forEach(function(discount) {
                    maxDiscount -= (discount * maxDiscount) / 100;
                });

                maxDiscount = ((maxDiscount - 100) * -1);

                return maxDiscount;
            }

            function calculateTotal(index) {
                let quantity = document.getElementById(`quantity-${index}`);
                let price = document.getElementById(`price-${index}`);
                let discountAmount = document.getElementById(`discountAmount-${index}`);
                let total = document.getElementById(`total-${index}`);
                let finalAmount = document.getElementById(`finalAmount-${index}`);

                let realQuantity = getRealQuantity(numberFormat(quantity.value), index);
                let currentFinalAmount = 0;

                if(quantity.value === "") {
                    totalAmount.value = thousandSeparator(numberFormat(totalAmount.value) - numberFormat(finalAmount.value));
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) - numberFormat(finalAmount.value));
                    total.value = '';
                    finalAmount.value = '';
                }
                else {
                    currentFinalAmount = numberFormat(finalAmount.value);
                    total.value = thousandSeparator(realQuantity * numberFormat(price.value));
                    finalAmount.value = thousandSeparator(realQuantity * numberFormat(price.value) - numberFormat(discountAmount.value));
                    calculateSubtotal(currentFinalAmount, numberFormat(finalAmount.value), subtotal, totalAmount);
                }

                calculateTax(numberFormat(subtotal.value));
            }

            function getRealQuantity(quantity, index) {
                let realQuantity = $(`#realQuantity-${index}`).val();

                return +quantity * +realQuantity;
            }

            function calculateSubtotal(previousAmount, currentAmount, subtotal, total) {
                if(previousAmount > currentAmount) {
                    total.value = thousandSeparator(numberFormat(total.value) - (+previousAmount - +currentAmount));
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) - (+previousAmount - +currentAmount));
                } else {
                    total.value = thousandSeparator(numberFormat(total.value) + (+currentAmount - +previousAmount));
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

            function changeReadonlyRequired(element) {
                element.attr('readonly', false);
                element.attr('required', true);
            }

            function updateAllRowIndexes(index, deleteRow) {
                let quantity = document.getElementById(`quantity-${index}`);
                let total = document.getElementById(`total-${index}`);
                let finalAmount = document.getElementById(`finalAmount-${index}`);

                if(quantity.value !== '') {
                    totalAmount.value = thousandSeparator(numberFormat(totalAmount.value) - numberFormat(finalAmount.value));
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) - numberFormat(finalAmount.value));
                    calculateTax(numberFormat(subtotal.value));
                }

                for(let i = index; i < deleteRow.length; i++) {
                    let unitValue = document.getElementById(`unitValue-${i}`);
                    let quantity = document.getElementById(`quantity-${i}`);
                    let realQuantity = document.getElementById(`realQuantity-${i}`);
                    let priceId = document.getElementById(`priceId-${i}`);
                    let price = document.getElementById(`price-${i}`);
                    let total = document.getElementById(`total-${i}`);
                    let discount = document.getElementById(`discount-${i}`);
                    let discountAmount = document.getElementById(`discountAmount-${i}`);
                    let finalAmount = document.getElementById(`finalAmount-${i}`);

                    let rowNumber = +i + 1;
                    let newProductId = document.getElementById(`productId-${rowNumber}`);
                    let newProductName = document.getElementById(`productId-${rowNumber}`);
                    let newQuantity = document.getElementById(`quantity-${rowNumber}`);
                    let newRealQuantity = document.getElementById(`realQuantity-${rowNumber}`);
                    let newUnit = document.getElementById(`unit-${rowNumber}`);
                    let newUnitValue = document.getElementById(`unitValue-${rowNumber}`);
                    let newPriceType = document.getElementById(`priceType-${rowNumber}`);
                    let newPriceId = document.getElementById(`priceId-${rowNumber}`);
                    let newPrice = document.getElementById(`price-${rowNumber}`);
                    let newTotal = document.getElementById(`total-${rowNumber}`);
                    let newDiscount = document.getElementById(`discount-${rowNumber}`);
                    let newDiscountAmount = document.getElementById(`discountAmount-${rowNumber}`);
                    let newFinalAmount = document.getElementById(`finalAmount-${rowNumber}`);

                    if(rowNumber !== deleteRow.length) {
                        quantity.value = newQuantity.value;
                        realQuantity.value = newRealQuantity.value;
                        unitValue.value = newUnitValue.value;
                        priceId.value = newPriceId.value;
                        price.value = newPrice.value;
                        total.value = newTotal.value;
                        discount.value = newDiscount.value;
                        discountAmount.value = newDiscountAmount.value;
                        finalAmount.value = newFinalAmount.value;

                        changeSelectPickerValue($(`#priceType-${i}`), newPriceType.value, rowNumber, true);
                        changeSelectPickerValue($(`#unit-${i}`), newUnit.value, rowNumber, true);
                        changeSelectPickerValue($(`#productName-${i}`), newProductName.value, rowNumber, false);
                        changeSelectPickerValue($(`#productId-${i}`), newProductId.value, rowNumber, false);

                        if(newProductId.value === '') {
                            let deletedElements = [quantity, price, discount];
                            handleDeletedElementAttribute(deletedElements);

                            updateDeletedRowValue([], i);
                        } else {
                            handleRemoveRequiredReadonly(newQuantity, quantity);
                            handleRemoveRequiredReadonly(newPrice, price);
                            handleRemoveRequiredReadonly(newDiscount, discount);
                        }

                        let elements = [
                            newFinalAmount,
                            newDiscountAmount,
                            newDiscount,
                            newTotal,
                            newPrice,
                            newPriceId,
                            newUnitValue,
                            newQuantity,
                            newRealQuantity
                        ];

                        updateDeletedRowValue(elements, rowNumber);
                    } else {
                        let totalRow = $('#rowNumber').val();
                        if(rowNumber > totalRow) {
                            $(`#${i}`).remove();
                        }

                        let deletedElements = [quantity, price, discount];
                        handleDeletedElementAttribute(deletedElements);

                        let elements = [
                            finalAmount,
                            discountAmount,
                            discount,
                            total,
                            price,
                            priceId,
                            unitValue,
                            quantity,
                            realQuantity
                        ];
                        
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

                removeSelectPickerOption($(`#priceType-${index}`), true);
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

            function newRowElement(rowId, rowNumber) {
                return `
                    <tr class="text-bold text-dark" id="${rowId}">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td>
                            <select class="selectpicker sales-order-sku-select-picker" name="product_id[]" id="productId-${rowId}" data-live-search="true" title="Enter Product SKU" tabindex="${rowNumber += 1}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="real_quantity[]" id="realQuantity-${rowId}">
                        </td>
                        <td>
                            <select class="selectpicker sales-order-name-select-picker" name="product_name[]" id="productName-${rowId}" data-live-search="true" title="Or Product Name..." tabindex="${rowNumber += 2}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->name }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="quantity[]" id="quantity-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('quantity[]') }}" tabindex="${rowNumber += 3}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
                        </td>
                        <td>
                            <select class="selectpicker sales-order-unit-select-picker" name="unit[]" id="unit-${rowId}" data-live-search="true" title="" tabindex="${rowNumber += 4}" disabled>
                            </select>
                            <input type="hidden" name="unit_id[]" id="unitValue-${rowId}">
                        </td>
                        <td>
                            <select class="selectpicker sales-order-price-type-select-picker" name="price_type[]" id="priceType-${rowId}" data-live-search="true" title="" tabindex="${rowNumber += 5}" disabled>
                            </select>
                            <input type="hidden" name="price_id[]" id="priceId-${rowId}">
                        </td>
                        <td>
                            <input type="text" name="price[]" id="price-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('price[]') }}" tabindex="${rowNumber += 6}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
                        </td>
                        <td>
                            <input type="text" name="total[]" id="total-${rowId}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('total[]') }}" title="" readonly >
                        </td>
                        <td>
                            <input type="text" name="discount[]" id="discount-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('discount[]') }}" tabindex="${rowNumber += 7}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers and plus sign" readonly>
                        </td>
                        <td>
                            <input type="text" name="discount_amount[]" id="discountAmount-${rowId}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('discount_amount[]') }}" title="" readonly >
                        </td>
                        <td>
                            <input type="text" name="final_amount[]" id="finalAmount-${rowId}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('final_amount[]') }}" title="" readonly >
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
