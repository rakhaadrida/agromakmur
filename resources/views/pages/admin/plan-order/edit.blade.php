@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Ubah Plan Order</h1>
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
                            <form action="{{ route('plan-orders.update', $planOrder->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="container so-container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-dark text-right">Nomor</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="number" id="number" value="{{ $planOrder->number }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col edit-receipt-general-info-right">
                                            <div class="form-group row sj-first-line">
                                                <label for="branch" class="col-5 col-form-label text-bold text-right text-dark">Cabang</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-5">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="branch" id="branch" value="{{ $planOrder->branch->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row sj-after-first">
                                                <label for="supplier" class="col-5 col-form-label text-bold text-right text-dark">Supplier</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="supplier" id="supplier" value="{{ $planOrder->supplier->name }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row po-update-left">
                                        <label for="date" class="col-2 col-form-label text-bold text-dark text-right">Tanggal</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="date" id="date" value="{{ formatDate($planOrder->date, 'd-m-Y') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row gr-update-input">
                                        <label for="description" class="col-2 col-form-label text-bold text-dark text-right">Deskripsi</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm mt-1 text-dark" name="description" id="description" tabindex="1" required autofocus>
                                            <input type="hidden" name="start_date" id="startDate" value="{{ $startDate }}">
                                            <input type="hidden" name="final_date" id="finalDate" value="{{ $finalDate }}">
                                            <input type="hidden" name="order_number" id="orderNumber" value="{{ $number }}">
                                            <input type="hidden" name="supplier_id" id="supplierId" value="{{ $supplierId }}">
                                            <input type="hidden" name="row_number" id="rowNumber" value="{{ $rowNumbers }}">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <span class="float-right mb-3 mr-2" id="addRow"><a href="#" class="text-primary text-bold">
                                    Tambah Baris <i class="fas fa-plus fa-lg ml-2" aria-hidden="true"></i></a>
                                </span>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" >
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td class="align-middle table-head-number-transaction">No</td>
                                            <td class="align-middle table-head-code-transaction">SKU</td>
                                            <td class="align-middle table-head-name-transaction">Nama Produk</td>
                                            <td class="align-middle table-head-quantity-transaction">Qty</td>
                                            <td class="align-middle table-head-unit-transaction">Unit</td>
                                            <td class="align-middle table-head-delete-transaction">Hapus</td>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTable">
                                        @foreach($planOrderItems as $key => $planOrderItem)
                                            <tr class="text-bold text-dark" id="{{ $key }}">
                                                <td class="align-middle text-center">{{ $key + 1 }}</td>
                                                <td>
                                                    <select class="selectpicker product-sku-plan-select-picker" name="product_id[]" id="productId-{{ $key }}" data-live-search="true" data-size="6" title="Input SKU Produk" tabindex="{{ $rowNumbers += 1 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}" @if($planOrderItem->product_id == $product->id) selected @endif>{{ $product->sku }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $key }}" value="{{ getRealQuantity($planOrderItem->quantity, $planOrderItem->actual_quantity) }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-name-plan-select-picker" name="product_name[]" id="productName-{{ $key }}" data-live-search="true" data-size="6" title="Atau Nama Produk..." tabindex="{{ $rowNumbers += 2 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->name }}" @if($planOrderItem->product_id == $product->id) selected @endif>{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="quantity[]" id="quantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatQuantity($planOrderItem->quantity) }}" tabindex="{{ $rowNumbers += 3 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-unit-select-picker" name="unit[]" id="unit-{{ $key }}" data-live-search="true" data-size="6" title="" tabindex="{{ $rowNumbers += 4 }}" @if($key == 0) required @endif>
                                                        @foreach($units[$planOrderItem->product_id] as $unit)
                                                            <option value="{{ $unit['id'] }}" data-tokens="{{ $unit['name'] }}" data-foo="{{ $unit['quantity'] }}" @if($planOrderItem->unit_id == $unit['id']) selected @endif>{{ $unit['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="unit_id[]" id="unitValue-{{ $key }}" value="{{ $planOrderItem->unit_id }}">
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
                                        <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit" tabindex="10000">Simpan</button>
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
                    <h4 class="modal-title text-bold">Notifikasi Produk</h4>
                </div>
                <div class="modal-body text-dark">
                    <h5>Ada kode produk yang duplikat seperti - (<span class="text-bold" id="duplicateCode"></span>). Harap tambahkan jumlah kode produk yang sama atau ubah kode produk.</h5>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        let url = new URL(window.location.href);
        let params = url.searchParams;

        params.delete('start_date');
        params.delete('final_date');
        params.delete('number');
        params.delete('supplier_id');

        window.history.pushState({}, document.title, url.toString());

        $(document).ready(function() {
            const table = $('#itemTable');

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

            table.on('change', 'select[name="unit[]"]', function () {
                const index = $(this).closest('tr').index();
                const selected = $(this).find(':selected');

                $(`#unitValue-${index}`).val(this.value);
                $(`#realQuantity-${index}`).val(selected.data('foo'));
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

                    $('#form').submit();
                }
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
                    },
                })
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

            function updateAllRowIndexes(index, deleteRow) {
                for(let i = index; i < deleteRow.length; i++) {
                    let quantity = document.getElementById(`quantity-${i}`);
                    let realQuantity = document.getElementById(`realQuantity-${i}`);
                    let unitValue = document.getElementById(`unitValue-${i}`);

                    let rowNumber = +i + 1;
                    let newProductId = document.getElementById(`productId-${rowNumber}`);
                    let newProductName = document.getElementById(`productId-${rowNumber}`);
                    let newQuantity = document.getElementById(`quantity-${rowNumber}`);
                    let newUnit = document.getElementById(`unit-${rowNumber}`);
                    let newRealQuantity = document.getElementById(`realQuantity-${rowNumber}`);
                    let newUnitValue = document.getElementById(`unitValue-${rowNumber}`);

                    if(rowNumber !== deleteRow.length) {
                        quantity.value = newQuantity.value;
                        realQuantity.value = newRealQuantity.value;
                        unitValue.value = newUnitValue.value;

                        changeSelectPickerValue($(`#unit-${i}`), newUnit.value, rowNumber, true);
                        changeSelectPickerValue($(`#productName-${i}`), newProductName.value, rowNumber, false);
                        changeSelectPickerValue($(`#productId-${i}`), newProductId.value, rowNumber, false);

                        if(newProductId.value === '') {
                            handleDeletedQuantity(quantity);
                            updateDeletedRowValue([], i);
                        } else {
                            newQuantity.removeAttribute('required');
                            quantity.removeAttribute('readonly');
                        }

                        let elements = [newQuantity, newRealQuantity, newUnitValue];
                        updateDeletedRowValue(elements, rowNumber);
                    } else {
                        let totalRow = $('#rowNumber').val();
                        if(rowNumber > totalRow) {
                            $(`#${i}`).remove();
                        }

                        handleDeletedQuantity(quantity);

                        let elements = [quantity, realQuantity, unitValue];
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

            function handleDeletedQuantity(quantity) {
                quantity.removeAttribute('required');
                quantity.readOnly = true;
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
                            <select class="selectpicker product-sku-plan-select-picker" name="product_id[]" id="productId-${rowId}" data-live-search="true" data-size="6" title="Input SKU Produk" tabindex="${rowNumbers += 1}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="real_quantity[]" id="realQuantity-${rowId}">
                        </td>
                        <td>
                            <select class="selectpicker product-name-plan-select-picker" name="product_name[]" id="productName-${rowId}" data-live-search="true" data-size="6" title="Atau Nama Produk..." tabindex="${rowNumbers += 2}">
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
