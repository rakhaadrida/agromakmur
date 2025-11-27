@extends('layouts.admin')

@push('addon-style')
  <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Product Transfer</h1>
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
                            <form action="{{ route('product-transfers.store') }}" method="POST" id="form">
                                @csrf
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Transfer Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" tabindex="1" class="form-control form-control-sm text-bold" name="number" id="number" value="{{ $number }}" data-old-value="{{ $number }}" autofocus required >
                                                </div>
                                                <label for="date" class="col-1 col-form-label text-bold text-right">Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" tabindex="2" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ $date }}" required>
                                                </div>
                                                <input type="hidden" name="row_number" id="rowNumber" value="{{ $rowNumbers }}">
                                                <input type="hidden" name="is_generated_number" id="isGeneratedNumber" value="1">
                                            </div>
                                        </div>
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
                                            <td class="align-middle table-head-code-transfer-transaction">SKU</td>
                                            <td class="align-middle table-head-name-transaction">Product Name</td>
                                            <td class="align-middle table-head-unit-transaction">Unit</td>
                                            <td class="align-middle table-head-warehouse-transaction">Source Warehouse</td>
                                            <td class="align-middle table-head-quantity-transaction">Original Stock</td>
                                            <td class="align-middle table-head-warehouse-transaction">Destination Warehouse</td>
                                            <td class="align-middle table-head-quantity-transaction">Destination Stock</td>
                                            <td class="align-middle table-head-quantity-transaction">Quantity Sent</td>
                                            <td class="align-middle table-head-delete-transaction">Delete</td>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTable">
                                        @foreach($rows as $key => $row)
                                            <tr class="text-bold text-dark" id="{{ $key }}">
                                                <td class="align-middle text-center">{{ $row }}</td>
                                                <td>
                                                    <select class="selectpicker product-sku-transfer-select-picker" name="product_id[]" id="productId-{{ $key }}" data-live-search="true" data-size="6" title="Enter SKU" tabindex="{{ $rowNumbers += 1 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $key }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-name-transfer-select-picker" name="product_name[]" id="productName-{{ $key }}" data-live-search="true" data-size="6" title="Or Product Name..." tabindex="{{ $rowNumbers += 2 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->name }}">{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-unit-transfer-select-picker" name="unit[]" id="unit-{{ $key }}" data-live-search="true" data-size="6" title="" tabindex="{{ $rowNumbers += 3 }}" disabled @if($key == 0) required @endif>
                                                    </select>
                                                    <input type="hidden" name="unit_id[]" id="unitValue-{{ $key }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-warehouse-transfer-select-picker" name="source_warehouse[]" id="sourceWarehouse-{{ $key }}" data-live-search="true" data-size="6" title="" tabindex="{{ $rowNumbers += 4 }}" @if($key == 0) required @endif disabled>
                                                    </select>
                                                    <input type="hidden" name="source_warehouse_id[]" id="sourceWarehouseId-{{ $key }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="source_stock[]" id="sourceStock-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" title="" readonly >
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-warehouse-transfer-select-picker" name="destination_warehouse[]" id="destinationWarehouse-{{ $key }}" data-live-search="true" data-size="6" title="" tabindex="{{ $rowNumbers += 5 }}" @if($key == 0) required @endif disabled>
                                                    </select>
                                                    <input type="hidden" name="destination_warehouse_id[]" id="destinationWarehouseId-{{ $key }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="destination_stock[]" id="destinationStock-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" title="" readonly >
                                                </td>
                                                <td>
                                                    <input type="text" name="quantity[]" id="quantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('quantity[]') }}" tabindex="{{ $rowNumbers += 6 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" readonly @if($key == 0) required @endif>
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
                                                <h4 class="modal-title">Product Transfer Confirmation</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>The Product Transfer data will be saved. Please select print or re-enter the product transfer.</p>
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
                    <h5>There are identical item codes with the same source and destination warehouses such as - (<span class="text-bold" id="duplicateCode"></span>). Please add up the quantities of the same item codes or change the warehouse selection of the similar item codes.</h5>
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
            let warehouses = @json($warehouses);
            let number = $('#number');
            const table = $('#itemTable');

            number.on('blur', function(event) {
                event.preventDefault();

                let oldValue = $(this).data('old-value');
                let currentValue = this.value;

                if(oldValue !== currentValue) {
                    $('#isGeneratedNumber').val(0);
                } else {
                    $('#isGeneratedNumber').val(1);
                }
            });

            table.on('change', 'select[name="product_id[]"]', function () {
                const index = $(this).closest('tr').index();
                displayStock(this.value, index, false);
            });

            table.on('change', 'select[name="product_name[]"]', function () {
                const index = $(this).closest('tr').index();
                displayStock(this.value, index, true);
            });

            table.on('change', 'select[name="unit[]"]', function () {
                const index = $(this).closest('tr').index();
                const selectedValue = $(this).find(':selected').data('foo');

                $(`#unitValue-${index}`).val(this.value);
                $(`#realQuantity-${index}`).val(selectedValue);

                let sourceStock = $(`#sourceWarehouse-${index}`).find('option:selected').data('foo');
                let destinationStock = $(`#destinationWarehouse-${index}`).find('option:selected').data('foo');

                $(`#sourceStock-${index}`).val(getActualStock(index, sourceStock));
                $(`#destinationStock-${index}`).val(getActualStock(index, destinationStock));
            });

            table.on('change', 'select[name="source_warehouse[]"]', function () {
                const index = $(this).closest('tr').index();
                const selectedValue = $(this).find(':selected').data('foo');

                let sourceStock = getActualStock(index, selectedValue);
                let sourceWarehouse = $(`#sourceWarehouse-${index}`);
                getDestinationWarehouse(index, this.value, sourceWarehouse);

                $(`#sourceWarehouseId-${index}`).val(this.value);
                $(`#sourceStock-${index}`).val(thousandSeparator(sourceStock));
            });

            table.on('change', 'select[name="destination_warehouse[]"]', function () {
                const index = $(this).closest('tr').index();
                const selectedValue = $(this).find(':selected').data('foo');

                let destinationStock = getActualStock(index, selectedValue);

                $(`#destinationWarehouseId-${index}`).val(this.value);
                $(`#destinationStock-${index}`).val(thousandSeparator(destinationStock));
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

                let duplicateCodes = checkDuplicateProduct();
                if(duplicateCodes.length) {
                    let duplicateCode = duplicateCodes.join(' | ');

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
                let rowNumbers = $('#rowNumber').val();
                rowNumbers = +rowNumbers + (+lastRowNumber * 24);

                let rowId = lastRowId ? +lastRowId + 1 : 1;
                let rowNumber = lastRowNumber ? +lastRowNumber + 1 : 1;
                let newRow = newRowElement(rowId, rowNumber, rowNumbers);

                itemTable.append(newRow);

                $(`#productId-${rowId}`).selectpicker();
                $(`#productName-${rowId}`).selectpicker();
            });

            function displayStock(productId, index, isProductName) {
                $.ajax({
                    url: '{{ route('products.index-ajax') }}',
                    type: 'GET',
                    data: {
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(data) {
                        let productName = $(`#productName-${index}`);
                        if(isProductName) {
                            productName = $(`#productId-${index}`);
                        }

                        let sourceWarehouse = $(`#sourceWarehouse-${index}`);
                        let sourceStock = $(`#sourceStock-${index}`);
                        let quantity = $(`#quantity-${index}`);
                        let productUnitId = data.data.unit_id;

                        productName.selectpicker('val', productId);
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

                        let warehouseId = 0;
                        let sourceQuantity = 0;
                        let productStocks = data.product_stocks;
                        sourceWarehouse.empty();

                        $.each(warehouses, function(key, item) {
                            sourceWarehouse.append(
                                $('<option></option>', {
                                    value: item.id,
                                    text: item.name,
                                    'data-tokens': item.name,
                                    'data-foo': productStocks[item.id]
                                })
                            );

                            if(!key) {
                                warehouseId = item.id;
                                sourceQuantity = productStocks[item.id] || 0;
                            }
                        });

                        sourceWarehouse.attr('disabled', false);
                        sourceWarehouse.selectpicker('refresh');
                        sourceWarehouse.selectpicker('val', warehouseId);
                        $(`#sourceWarehouseId-${index}`).val(warehouseId);

                        sourceStock.val(thousandSeparator(sourceQuantity || 0));
                        getDestinationWarehouse(index, warehouseId, sourceWarehouse);
                    },
                })
            }

            function getDestinationWarehouse(index, value, sourceWarehouse) {
                const destinationWarehouse = $(`#destinationWarehouse-${index}`);
                const filteredWarehouses = warehouses.filter(item => item.id !== +value);

                let destinationStock = $(`#destinationStock-${index}`);
                let warehouseId = 0;
                let destinationQuantity = 0;
                destinationWarehouse.empty();

                $.each(filteredWarehouses, function(key, item) {
                    let productStocks = sourceWarehouse.find(`option[value="${item.id}"]`).data('foo');

                    destinationWarehouse.append(
                        $('<option></option>', {
                            value: item.id,
                            text: item.name,
                            'data-tokens': item.name,
                            'data-foo': productStocks,
                        })
                    );

                    if(!key) {
                        warehouseId = item.id;
                        destinationQuantity = productStocks || 0;
                    }
                });

                destinationWarehouse.attr('disabled', false);
                destinationWarehouse.selectpicker('refresh');
                destinationWarehouse.selectpicker('val', warehouseId);
                $(`#destinationWarehouseId-${index}`).val(warehouseId);

                let actualStock = getActualStock(index, destinationQuantity || 0);
                destinationStock.val(thousandSeparator(actualStock));
            }

            function getActualStock(index, quantity) {
                let realQuantity = $(`#realQuantity-${index}`).val();
                let actualStock = decimalFormat(+quantity / +realQuantity);

                return thousandSeparator(actualStock);
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

            function decimalFormat(value) {
                let stringValue = value.toString();

                return stringValue.replace(/\./g, ",");
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
                for(let i = index; i < deleteRow.length; i++) {
                    let unitValue = document.getElementById(`unitValue-${i}`);
                    let sourceWarehouseId = document.getElementById(`sourceWarehouseId-${i}`);
                    let destinationWarehouseId = document.getElementById(`destinationWarehouseId-${i}`);
                    let sourceStock = document.getElementById(`sourceStock-${i}`);
                    let destinationStock = document.getElementById(`destinationStock-${i}`);
                    let quantity = document.getElementById(`quantity-${i}`);
                    let realQuantity = document.getElementById(`realQuantity-${i}`);

                    let rowNumber = +i + 1;
                    let newProductId = document.getElementById(`productId-${rowNumber}`);
                    let newProductName = document.getElementById(`productId-${rowNumber}`);
                    let newUnit = document.getElementById(`unit-${rowNumber}`);
                    let newUnitValue = document.getElementById(`unitValue-${rowNumber}`);
                    let newSourceWarehouse = document.getElementById(`sourceWarehouse-${rowNumber}`);
                    let newSourceWarehouseId = document.getElementById(`sourceWarehouseId-${rowNumber}`);
                    let newSourceStock = document.getElementById(`sourceStock-${rowNumber}`);
                    let newDestinationWarehouse = document.getElementById(`destinationWarehouse-${rowNumber}`);
                    let newDestinationWarehouseId = document.getElementById(`destinationWarehouseId-${rowNumber}`);
                    let newDestinationStock = document.getElementById(`destinationStock-${rowNumber}`);
                    let newQuantity = document.getElementById(`quantity-${rowNumber}`);
                    let newRealQuantity = document.getElementById(`realQuantity-${rowNumber}`);

                    if(rowNumber !== deleteRow.length) {
                        unitValue.value = newUnitValue.value;
                        sourceWarehouseId.value = newSourceWarehouseId.value;
                        destinationWarehouseId.value = newDestinationWarehouseId.value;
                        sourceStock.value = newSourceStock.value;
                        destinationStock.value = newDestinationStock.value;
                        quantity.value = newQuantity.value;
                        realQuantity.value = newRealQuantity.value;

                        changeSelectPickerValue($(`#productName-${i}`), newProductName.value, $(`#productName-${rowNumber}`), false);
                        changeSelectPickerValue($(`#productId-${i}`), newProductId.value, $(`#productId-${rowNumber}`), false);
                        changeSelectPickerValue($(`#unit-${i}`), newUnit.value, $(`#unit-${rowNumber}`), true);
                        changeSelectPickerValue($(`#sourceWarehouse-${i}`), newSourceWarehouse.value, $(`#sourceWarehouse-${rowNumber}`), true);
                        changeSelectPickerValue($(`#destinationWarehouse-${i}`), newDestinationWarehouse.value, $(`#destinationWarehouse-${rowNumber}`), true);

                        if(newProductId.value === '') {
                            handleDeletedQuantity(quantity);
                            updateDeletedRowValue([], i);
                        } else {
                            newQuantity.removeAttribute('required');
                            quantity.removeAttribute('readonly');
                        }

                        let elements = [
                            newUnitValue,
                            newSourceWarehouseId,
                            newSourceStock,
                            newDestinationWarehouseId,
                            newDestinationStock,
                            newQuantity,
                            newRealQuantity
                        ];
                        updateDeletedRowValue(elements, rowNumber);
                    } else {
                        let totalRow = $('#rowNumber').val();
                        if(rowNumber > totalRow) {
                            $(`#${i}`).remove();
                        }

                        handleDeletedQuantity(quantity);

                        let elements = [
                            unitValue,
                            sourceWarehouseId,
                            sourceStock,
                            destinationWarehouseId,
                            destinationStock,
                            quantity,
                            realQuantity,
                        ];
                        updateDeletedRowValue(elements, i);
                    }
                }
            }

            function changeSelectPickerValue(selectElement, value, newElement, isRemoveDisabled) {
                if(isRemoveDisabled) {
                    if(!newElement.is(':disabled')) {
                        selectElement.empty();
                        selectElement.append(newElement.html()).find('option');
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

                removeSelectPickerOption($(`#productName-${index}`), false);
                removeSelectPickerOption($(`#productId-${index}`), false);
                removeSelectPickerOption($(`#unit-${index}`), true);
                removeSelectPickerOption($(`#sourceWarehouse-${index}`), true);
                removeSelectPickerOption($(`#destinationWarehouse-${index}`), true);
            }

            function handleDeletedQuantity(quantity) {
                quantity.removeAttribute('required');
                quantity.readOnly = true;
            }

            function checkDuplicateProduct() {
                let productIdElements = $('select[name="product_id[]"]');

                let productWarehouseIds = [];
                let productWarehouseDuplicates = [];

                productIdElements.each(function(index) {
                    let productId = $(this).val();
                    let productSku = $(this).find(':selected').data('tokens');

                    let sourceWarehouse = $(`#sourceWarehouse-${index}`);
                    let sourceWarehouseName = sourceWarehouse.find('option:selected').data('tokens');

                    let destinationWarehouse = $(`#destinationWarehouse-${index}`);
                    let destinationWarehouseName = destinationWarehouse.find(':selected').data('tokens');

                    if(productId) {
                        let productWarehouse = `${productId}-${sourceWarehouse.val()}-${destinationWarehouse.val()}`;

                        if(productWarehouseIds.includes(productWarehouse)) {
                            let productDuplicates = `${productSku}-${sourceWarehouseName}-${destinationWarehouseName}`
                            productWarehouseDuplicates.push(productDuplicates);
                        } else {
                            productWarehouseIds.push(productWarehouse);
                        }
                    }
                });

                return [...new Set(productWarehouseDuplicates)];
            }

            function newRowElement(rowId, rowNumber, rowNumbers) {
                return `
                    <tr class="text-bold text-dark" id="${rowId}">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td>
                            <select class="selectpicker product-sku-transfer-select-picker" name="product_id[]" id="productId-${rowId}" data-live-search="true" data-size="6" title="Enter SKU" tabindex="${rowNumbers += 1}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="real_quantity[]" id="realQuantity-${rowId}">
                        </td>
                        <td>
                            <select class="selectpicker product-name-transfer-select-picker" name="product_name[]" id="productName-${rowId}" data-live-search="true" data-size="6" title="Or Product Name..." tabindex="${rowNumbers += 2}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->name }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="selectpicker product-unit-transfer-select-picker" name="unit[]" id="unit-${rowId}" data-live-search="true" data-size="6" title="" tabindex="${rowNumbers += 3}" disabled>
                            </select>
                            <input type="hidden" name="unit_id[]" id="unitValue-${rowId}">
                        </td>
                        <td>
                            <select class="selectpicker product-warehouse-transfer-select-picker" name="source_warehouse[]" id="sourceWarehouse-${rowId}" data-live-search="true" data-size="6" title="" tabindex="${rowNumbers += 4}" disabled>
                            </select>
                            <input type="hidden" name="source_warehouse_id[]" id="sourceWarehouseId-${rowId}">
                        </td>
                        <td>
                            <input type="text" name="source_stock[]" id="sourceStock-${rowId}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" title="" readonly >
                        </td>
                        <td>
                            <select class="selectpicker product-warehouse-transfer-select-picker" name="destination_warehouse[]" id="destinationWarehouse-${rowId}" data-live-search="true" data-size="6" title="" tabindex="${rowNumbers += 5}" disabled>
                            </select>
                            <input type="hidden" name="destination_warehouse_id[]" id="destinationWarehouseId-${rowId}">
                        </td>
                        <td>
                            <input type="text" name="destination_stock[]" id="destinationStock-${rowId}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" title="" readonly >
                        </td>
                        <td>
                            <input type="text" name="quantity[]" id="quantity-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('quantity[]') }}" tabindex="${rowNumbers += 6}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" readonly>
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
