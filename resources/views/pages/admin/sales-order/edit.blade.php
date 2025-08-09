@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Edit Sales Order</h1>
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
                            <form action="{{ route('sales-orders.update', $salesOrder->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="container so-container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-dark text-right">Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark" name="number" id="number" value="{{ $salesOrder->number }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col edit-receipt-general-info-right">
                                            <div class="form-group row so-update-customer">
                                                <label for="customer" class="col-3 col-form-label text-bold text-right text-dark">Customer</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <select class="selectpicker warehouse-select-picker" name="customer_id" id="customer" data-live-search="true" title="Enter or Choose Customer" tabindex="3" required>
                                                        @foreach($customers as $customer)
                                                            <option value="{{ $customer->id }}" data-tokens="{{ $customer->name }}" data-foo="{{ $customer->marketing_id }}" data-tax="{{ $customer->tax_number }}" data-tempo="{{ $customer->tempo }}" @if($salesOrder->customer_id == $customer->id) selected @endif>{{ $customer->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('customer')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row so-update-input">
                                                <label for="marketing" class="col-3 col-form-label text-bold text-right text-dark">Marketing</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <select class="selectpicker marketing-select-picker" name="marketing_id" id="marketing" data-live-search="true" title="Enter or Choose Marketing" tabindex="4" required>
                                                        @foreach($marketings as $marketing)
                                                            <option value="{{ $marketing->id }}" data-tokens="{{ $marketing->name }}" @if($salesOrder->marketing_id == $marketing->id) selected @endif>{{ $marketing->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('marketing')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row so-update-input">
                                                <label for="tempo" class="col-3 col-form-label text-bold text-right text-dark">Tempo</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-3 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold" name="tempo" id="tempo" value="{{ $salesOrder->tempo }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" tabindex="5">
                                                </div>
                                                <span class="col-form-label text-bold"> Day(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row so-update-date">
                                        <label for="date" class="col-2 col-form-label text-bold text-dark text-right">Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ formatDate($salesOrder->date, 'd-m-Y') }}" tabindex="2" required>
                                        </div>
                                    </div>
                                    <div class="form-group row so-update-input">
                                        <label for="description" class="col-2 col-form-label text-bold text-dark text-right">Description</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4">
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
                                        @foreach($salesOrderItems as $key => $salesOrderItem)
                                            <tr class="text-bold text-dark" id="{{ $key }}">
                                                <td class="align-middle text-center">{{ $key + 1 }}</td>
                                                <td>
                                                    <select class="selectpicker sales-order-sku-select-picker" name="product_id[]" id="productId-{{ $key }}" data-live-search="true" title="Enter SKU" tabindex="{{ $rowNumbers += 5 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}" @if($salesOrderItem->product_id == $product->id) selected @endif>{{ $product->sku }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $key }}" value="{{ getRealQuantity($salesOrderItem->quantity, $salesOrderItem->actual_quantity) }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker sales-order-name-select-picker" name="product_name[]" id="productName-{{ $key }}" data-live-search="true" title="Or Product Name..." tabindex="{{ $rowNumbers += 6 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->name }}" @if($salesOrderItem->product_id == $product->id) selected @endif>{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="base_warehouse_ids[]" id="baseWarehouseIds-{{ $key }}" value="{{ $salesOrderItem->warehouse_ids }}">
                                                    <input type="hidden" name="base_warehouse_stocks[]" id="baseWarehouseStocks-{{ $key }}" value="{{ $salesOrderItem->warehouse_stocks }}">
                                                    <input type="hidden" name="warehouse_ids[]" id="warehouseIds-{{ $key }}" value="{{ $salesOrderItem->warehouse_ids }}">
                                                    <input type="hidden" name="warehouse_stocks[]" id="warehouseStocks-{{ $key }}" value="{{ $salesOrderItem->warehouse_stocks }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="quantity[]" id="quantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatQuantity($salesOrderItem->quantity) }}" data-foo="{{ $salesOrderItem->quantity }}" tabindex="{{ $rowNumbers += 7 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <select class="selectpicker sales-order-unit-select-picker" name="unit[]" id="unit-{{ $key }}" data-live-search="true" title="" tabindex="{{ $rowNumbers += 7 }}" @if($key == 0) required @endif>
                                                        @foreach($units[$salesOrderItem->product_id] as $unit)
                                                            <option value="{{ $unit['id'] }}" data-tokens="{{ $unit['name'] }}" data-foo="{{ $unit['quantity'] }}" @if($salesOrderItem->unit_id == $unit['id']) selected @endif>{{ $unit['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="unit_id[]" id="unitValue-{{ $key }}" value="{{ $salesOrderItem->unit_id }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker sales-order-price-type-select-picker" name="price_type[]" id="priceType-{{ $key }}" data-live-search="true" title="" tabindex="{{ $rowNumbers += 6 }}" @if($key == 0) required @endif>
                                                        @foreach($prices[$salesOrderItem->product_id] as $price)
                                                            <option value="{{ $price['id'] }}" data-tokens="{{ $price['code'] }}" data-foo="{{ $price['price'] }}" @if($salesOrderItem->price_id == $price['id']) selected @endif>{{ $price['code'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="price_id[]" id="priceId-{{ $key }}" value="{{ $salesOrderItem->price_id }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="price[]" id="price-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatPrice($salesOrderItem->price) }}" tabindex="{{ $rowNumbers += 8 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="total[]" id="total-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ formatPrice($salesOrderItem->total) }}" title="" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="discount[]" id="discount-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ $salesOrderItem->discount }}" tabindex="{{ $rowNumbers += 9 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers and plus sign" @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="discount_product[]" id="discountProduct-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ formatPrice($salesOrderItem->discount_amount) }}" title="" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="final_amount[]" id="finalAmount-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ formatPrice($salesOrderItem->final_amount) }}" title="" readonly>
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
                                        <input type="text" class="form-control-plaintext form-control-sm text-bold text-secondary text-right mt-1" name="total_amount" id="totalAmount" value="{{ formatPrice($salesOrder->subtotal) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so sales-order-total-amount-info">
                                    <label for="invoiceDiscount" class="col-3 col-form-label text-bold text-right text-dark">Invoice Discount</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <span class="col-form-label text-bold ml-2">Rp</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right mt-1 invoice-discount" name="invoice_discount" id="invoiceDiscount" value="{{ formatPrice($salesOrder->discount_amount) }}" tabindex="9999">
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so sales-order-total-amount-info">
                                    <label for="subtotal" class="col-3 col-form-label text-bold text-right text-dark">Sub Total</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <span class="col-form-label text-bold ml-2">Rp</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control-plaintext form-control-sm text-bold text-secondary text-right mt-1" name="subtotal" id="subtotal" value="{{ formatPrice($salesOrder->subtotal - $salesOrder->discount_amount) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so sales-order-total-amount-info">
                                    <label for="taxAmount" class="col-3 col-form-label text-bold text-right text-dark">Tax Amount</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <span class="col-form-label text-bold ml-2">Rp</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control-plaintext form-control-sm text-bold text-danger text-right mt-1" name="tax_amount" id="taxAmount" value="{{ formatPrice($salesOrder->tax_amount) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so sales-order-total-amount-info">
                                    <label for="grandTotal" class="col-3 col-form-label text-bold text-right text-dark">Grand Total</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <span class="col-form-label text-bold ml-2">Rp</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control-plaintext form-control-sm text-bold text-danger text-right mt-1" name="grand_total" id="grandTotal" value="{{ formatPrice($salesOrder->grand_total) }}" readonly>
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

    <div class="modal" id="modalStock" tabindex="-1" role="dialog" aria-labelledby="modalStock" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="h2 text-bold">&times;</span>
                    </button>
                    <h4 class="modal-title text-bold">Product Stock Notification</h4>
                </div>
                <div class="modal-body text-dark">
                    <h5>The input quantity cannot exceed the total stock. The total stock for the item <span class="col-form-label text-bold" id="stockProductName"></span> is <span class="col-form-label text-bold" id="totalStock"></span> (<span class="col-form-label text-bold" id="totalConversion"></span>)</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalWarehouseStock" tabindex="-1" role="dialog" aria-labelledby="modalWarehouseStock" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" id="closeModal" class="h2 text-bold">&times;</span>
                    </button>
                    <h4 class="modal-title text-bold">Choose Warehouse Stock</h4>
                </div>
                <div class="modal-body text-dark">
                    <p>Order quantity exceeds stock in warehouse <span class="text-bold" id="warehouseName"></span>. Select another warehouse to fulfill the order quantity.</p>
                    <div class="form-group row" style="margin-top: -10px">
                        <label for="kode" class="col-6 col-form-label text-bold">Order Quantity</label>
                        <span class="col-auto col-form-label text-bold">:</span>
                        <span class="col-form-label text-bold" id="orderQuantity"></span>
                        <input type="hidden" id="rowIndex">
                    </div>
                    <div class="form-group row" style="margin-top: -20px">
                        <label for="kode" class="col-6 col-form-label text-bold">Primary Warehouse Stock</label>
                        <span class="col-auto col-form-label text-bold">:</span>
                        <span class="col-form-label text-bold" id="primaryStock"></span>
                    </div>
                    <div class="form-group row" style="margin-top: -20px">
                        <label for="kode" class="col-6 col-form-label text-bold">Remaining Order Quantity</label>
                        <span class="col-auto col-form-label text-bold">:</span>
                        <span class="col-form-label text-bold" id="remainingQuantity"></span>
                        <input type="hidden" id="remainingQuantityValue">
                        <input type="hidden" id="remainingQuantityUnit">
                        <input type="hidden" id="remainingConversionValue">
                        <input type="hidden" id="remainingConversionUnit">
                    </div>
                    <label style="margin-bottom: -5px">Choose Another Warehouse</label>
                    @foreach($warehouses as $key => $warehouse)
                        <div class="row">
                            <label for="warehouseName" class="col-8 col-form-label text-bold">{{ $warehouse->name }} (Stock : <span class="col-form-label text-bold" id="warehouseStock-{{ $warehouse->id }}"></span>)</label>
                            <input type="hidden" id="warehouseId-{{ $warehouse->id }}" value="{{ $warehouse->id }}">
                            <input type="hidden" id="warehouseOriginalStock-{{ $warehouse->id }}">
                            <div class="col-3">
                                <button type="button" class="btn btn-sm btn-success btn-block text-bold mt-1 btn-select" id="btnSelect-{{ $warehouse->id }}">Select</button>
                            </div>
                        </div>
                    @endforeach
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
            const modalWarehouseStock = $('#modalWarehouseStock');

            let invoiceDiscount = $('#invoiceDiscount');
            let totalAmount = document.getElementById('totalAmount');
            let subtotal = document.getElementById('subtotal');

            $('#customer').on('change', function(event) {
                event.preventDefault();

                const selected = $(this).find(':selected');
                const marketing = $(`#marketing`);

                marketing.selectpicker('val', selected.data('foo'));
                marketing.selectpicker('refresh');

                $('#taxNumber').val(selected.data('tax'));
                $('#tempo').val(selected.data('tempo'));
            });

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
                let baseQuantity = $(this).data('foo') || 0;

                checkProductStock(index, this.value, baseQuantity);
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

                $(`#priceId-${index}`).val(selected.val());
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

            invoiceDiscount.on('keyup', function() {
                this.value = currencyFormat(this.value);
            });

            invoiceDiscount.on('blur', function() {
                calculateInvoiceDiscount(this.value, totalAmount.value);
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

                $('input[name="discount_product[]"]').each(function() {
                    this.value = numberFormat(this.value);
                });

                let invoiceDiscount = $('#invoiceDiscount');
                invoiceDiscount.val(numberFormat(invoiceDiscount.val()));

                let duplicateCodes = checkDuplicateProduct();
                if(duplicateCodes.length) {
                    let duplicateCode = duplicateCodes.join(', ');

                    $('#duplicateCode').text(duplicateCode);
                    $('#modalDuplicate').modal('show');

                    return false;
                } else {
                    $('#form').submit();
                }
            });

            $('#addRow').on('click', function(event) {
                event.preventDefault();

                let itemTable = $('#itemTable');
                let lastRowId = itemTable.find('tr:last').attr('id');
                let lastRowNumber = itemTable.find('tr:last td:first-child').text();
                let rowNumbers = $('#rowNumber').val();
                rowNumbers = +rowNumbers + (+lastRowNumber * 55);

                let rowId = lastRowId ? +lastRowId + 1 : 1;
                let rowNumber = lastRowNumber ? +lastRowNumber + 1 : 1;
                let newRow = newRowElement(rowId, rowNumber, rowNumbers);

                itemTable.append(newRow);

                $(`#productId-${rowId}`).selectpicker();
                $(`#productName-${rowId}`).selectpicker();
            });

            modalWarehouseStock.on('click', '.btn-select', function () {
                const buttonId = $(this).attr('id');
                const warehouseId = buttonId.split('-')[1];

                updateWarehouseStock(warehouseId, this);
            });

            modalWarehouseStock.on('hidden.bs.modal', function () {
                $('.btn-select').each(function () {
                    $(this).attr('disabled', false);
                });
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
                        let productPriceId = data.main_price_id;

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

            function checkProductStock(index, quantity, baseQuantity) {
                let productId = $(`#productId-${index} option:selected`).val();
                let productName = $(`#productName-${index} option:selected`).text();
                let selectedUnit = $(`#unit-${index} option:selected`);
                let conversionUnit = $(`#unit-${index} option:not(:selected):first`);
                let warehouseIds = $(`#warehouseIds-${index}`);
                let warehouseStocks = $(`#warehouseStocks-${index}`);
                let baseWarehouseIds = $(`#baseWarehouseIds-${index}`) || [];
                let baseWarehouseStocks = $(`#baseWarehouseStocks-${index}`) || [];

                let totalStock = 0;
                let productStocks;
                let currentQuantity = numberFormat(quantity) || 0;

                quantity = numberFormat(quantity) - +baseQuantity;

                $.ajax({
                    url: '{{ route('products.check-stock-ajax') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(data) {
                        let primaryWarehouse = data.primary_warehouse;
                        let otherWarehouses = data.other_warehouses;
                        let arrayWarehouseIds = baseWarehouseIds.length ? baseWarehouseIds.val().split(',') : [];
                        let arrayWarehouseStocks = baseWarehouseStocks.length ? baseWarehouseStocks.val().split(',') : [];
                        let primaryWarehouseStock = arrayWarehouseStocks[0] || 0;
                        let primaryWarehouseConversion = +primaryWarehouseStock / +conversionUnit.data('foo');

                        totalStock = data.total_stock;
                        productStocks = data.product_stocks;

                        warehouseIds.val(primaryWarehouse.id);
                        warehouseStocks.val(+quantity + +primaryWarehouseStock);

                        let baseConversionStock = +baseQuantity / +conversionUnit.data('foo');
                        let conversionStock = +totalStock / +conversionUnit.data('foo');

                        if(+quantity > +totalStock) {
                            totalStock = thousandSeparator(+totalStock + +baseQuantity) + ` ${selectedUnit.text()}`;
                            conversionStock = thousandSeparator(+conversionStock + +baseConversionStock) + ` ${conversionUnit.text()}`;

                            $('#stockProductName').text(productName);
                            $('#totalStock').text(totalStock);
                            $('#totalConversion').text(` ${conversionStock}`);
                            $('#modalStock').modal('show');
                        } else if(+quantity > +primaryWarehouse.stock) {
                            let originalQuantity = thousandSeparator(+quantity + +baseQuantity) + ` ${selectedUnit.text()}`
                            let orderConversion = +quantity / +conversionUnit.data('foo');
                            let conversionQuantity = ` (${thousandSeparator(+orderConversion + +baseConversionStock)} ${conversionUnit.text()})`;
                            let orderQuantity = originalQuantity + conversionQuantity;

                            let primaryQuantity = thousandSeparator(+primaryWarehouse.stock + +primaryWarehouseStock) + ` ${selectedUnit.text()}`;
                            let primaryConversionStock = +primaryWarehouse.stock / +conversionUnit.data('foo');
                            let primaryConversion = ` (${thousandSeparator(+primaryConversionStock + +primaryWarehouseConversion)} ${conversionUnit.text()})`;
                            let primaryStock = primaryQuantity + primaryConversion;

                            let remainingStockValue = (+quantity + +baseQuantity) - (+primaryWarehouse.stock + +primaryWarehouseStock);
                            let remainingQuantity = thousandSeparator(remainingStockValue) + ` ${selectedUnit.text()}`;
                            let remainingConversionStock = +remainingStockValue / +conversionUnit.data('foo');
                            let remainingConversion = ` (${thousandSeparator(remainingConversionStock)} ${conversionUnit.text()})`;
                            let remainingStock = remainingQuantity + remainingConversion;

                            $('#warehouseName').text(primaryWarehouse.name);
                            $('#orderQuantity').text(orderQuantity);
                            $('#primaryStock').text(primaryStock);
                            $('#remainingQuantity').text(remainingStock);

                            $('#remainingQuantityValue').val(remainingStockValue);
                            $('#remainingQuantityUnit').val(selectedUnit.text());
                            $('#remainingConversionValue').val(conversionUnit.data('foo'));
                            $('#remainingConversionUnit').val(conversionUnit.text());

                            $.each(otherWarehouses, function(key, item) {
                                let existingWarehouseStock = 0;
                                let existingWarehouseConversion = 0;
                                let existingWarehouseId = arrayWarehouseIds.indexOf(item.id.toString());

                                if(existingWarehouseId > 0) {
                                    existingWarehouseStock = arrayWarehouseStocks[existingWarehouseId];
                                    existingWarehouseConversion = existingWarehouseStock / +conversionUnit.data('foo');
                                }

                                let otherStock = thousandSeparator(+item.stock + +existingWarehouseStock) + ` ${selectedUnit.text()} / `;
                                let otherConversionStock = +item.stock / +conversionUnit.data('foo');
                                let otherConversion = `${thousandSeparator(+otherConversionStock + +existingWarehouseConversion)} ${conversionUnit.text()}`;

                                $(`#warehouseStock-${item.id}`).text(otherStock + otherConversion);
                                $(`#warehouseOriginalStock-${item.id}`).val(+item.stock + +existingWarehouseStock);
                            });

                            $('#rowIndex').val(index);
                            warehouseStocks.val(+primaryWarehouse.stock + +primaryWarehouseStock);
                            modalWarehouseStock.modal('show');
                        } else {
                            if(arrayWarehouseIds.length > 0) {
                                let newWarehouseIds = '';
                                let newWarehouseStocks = '';
                                let newQuantity = +quantity + +primaryWarehouseStock;

                                arrayWarehouseIds.forEach(function (warehouseId, index) {
                                    if (!index) {
                                        newWarehouseIds = warehouseId;
                                        newWarehouseStocks = newQuantity > 0 ? newQuantity : 0;
                                        currentQuantity -= newWarehouseStocks;
                                    } else {
                                        let newStocks = arrayWarehouseStocks[index];
                                        if (currentQuantity > arrayWarehouseStocks[index]) {
                                            currentQuantity -= arrayWarehouseStocks[index];
                                        } else {
                                            newStocks = currentQuantity;
                                            currentQuantity = 0;
                                        }

                                        newWarehouseIds += ',' + warehouseId;
                                        newWarehouseStocks += ',' + newStocks;
                                    }
                                });

                                warehouseIds.val(newWarehouseIds);
                                warehouseStocks.val(newWarehouseStocks);
                            }
                        }
                    },
                })
            }

            function updateWarehouseStock(index, element) {
                let remainingStockValue = $('#remainingQuantityValue');
                let remainingStockUnit = $('#remainingQuantityUnit');
                let remainingConversionValue = $('#remainingConversionValue');
                let remainingConversionUnit = $('#remainingConversionUnit');
                let warehouseOriginalStock = $(`#warehouseOriginalStock-${index}`);
                let warehouseId = $(`#warehouseId-${index}`).val();
                let rowIndex = $('#rowIndex').val();
                let warehouseIds = $(`#warehouseIds-${rowIndex}`);
                let warehouseStocks = $(`#warehouseStocks-${rowIndex}`);

                let remainingStock = +remainingStockValue.val();
                let remainingConversion = +remainingConversionValue.val();
                let warehouseStock = warehouseOriginalStock.val();
                let warehouseIdsValue = warehouseIds.val();
                let warehouseStocksValue = warehouseStocks.val();

                if(+warehouseStock < +remainingStock) {
                    $(element).attr('disabled', true);

                    let newRemainingStock = +remainingStock - +warehouseStock;
                    let newRemainingConversion = +newRemainingStock / +remainingConversion;

                    let remainingQuantityText = thousandSeparator(newRemainingStock) + ` ${remainingStockUnit.val()}`;
                    let remainingConversionText = ` (${thousandSeparator(newRemainingConversion)} ${remainingConversionUnit.val()})`;
                    let newRemainingQuantity = remainingQuantityText + remainingConversionText;

                    warehouseIds.val(warehouseIdsValue + ',' + warehouseId);
                    warehouseStocks.val(warehouseStocksValue + ',' + warehouseStock);

                    remainingStockValue.val(newRemainingStock);
                    $('#remainingQuantity').text(newRemainingQuantity);
                } else {
                    warehouseIds.val(warehouseIdsValue + ',' + warehouseId);
                    warehouseStocks.val(warehouseStocksValue + ',' + remainingStock);

                    modalWarehouseStock.modal('hide');
                }
            }

            function calculateTotal(index) {
                let quantity = document.getElementById(`quantity-${index}`);
                let price = document.getElementById(`price-${index}`);
                let discountProduct = document.getElementById(`discountProduct-${index}`);
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
                    finalAmount.value = thousandSeparator(realQuantity * numberFormat(price.value) - numberFormat(discountProduct.value));
                    calculateSubtotal(currentFinalAmount, numberFormat(finalAmount.value), subtotal, totalAmount);
                }

                calculateTax(numberFormat(subtotal.value));
            }

            function getRealQuantity(quantity, index) {
                let realQuantity = $(`#realQuantity-${index}`).val();

                return +quantity * +realQuantity;
            }

            function calculateDiscount(index) {
                let discount = document.getElementById(`discount-${index}`);
                let discountProduct = document.getElementById(`discountProduct-${index}`);
                let finalAmount = document.getElementById(`finalAmount-${index}`);
                let total = document.getElementById(`total-${index}`);

                if(discount.value === '') {
                    totalAmount.value = thousandSeparator(numberFormat(totalAmount.value) + numberFormat(discountProduct.value));
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) + numberFormat(discountProduct.value));
                    discountProduct.value = '';
                    finalAmount.value = total.value;
                } else {
                    let currentFinalAmount = numberFormat(finalAmount.value);
                    let discountPercentage = calculateDiscountPercentage(discount.value);
                    let totalValue = numberFormat(total.value);
                    let discountValue = ((discountPercentage * totalValue) / 100).toFixed(0);

                    discountProduct.value = thousandSeparator(discountValue);
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

            function calculateInvoiceDiscount(invoiceDiscount, totalAmount) {
                subtotal.value = thousandSeparator(numberFormat(totalAmount) - numberFormat(invoiceDiscount));
                calculateTax(numberFormat(subtotal.value));
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
                    let discountProduct = document.getElementById(`discountProduct-${i}`);
                    let finalAmount = document.getElementById(`finalAmount-${i}`);
                    let warehouseIds = document.getElementById(`warehouseIds-${i}`);
                    let warehouseStocks = document.getElementById(`warehouseStocks-${i}`);

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
                    let newDiscountProduct = document.getElementById(`discountProduct-${rowNumber}`);
                    let newFinalAmount = document.getElementById(`finalAmount-${rowNumber}`);
                    let newWarehouseIds = document.getElementById(`warehouseIds-${rowNumber}`);
                    let newWarehouseStocks = document.getElementById(`warehouseStocks-${rowNumber}`);

                    if(rowNumber !== deleteRow.length) {
                        quantity.value = newQuantity.value;
                        realQuantity.value = newRealQuantity.value;
                        unitValue.value = newUnitValue.value;
                        priceId.value = newPriceId.value;
                        price.value = newPrice.value;
                        total.value = newTotal.value;
                        discount.value = newDiscount.value;
                        discountProduct.value = newDiscountProduct.value;
                        finalAmount.value = newFinalAmount.value;
                        warehouseIds.value = newWarehouseIds.value;
                        warehouseStocks.value = newWarehouseStocks.value;

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
                            newDiscountProduct,
                            newDiscount,
                            newTotal,
                            newPrice,
                            newPriceId,
                            newUnitValue,
                            newQuantity,
                            newRealQuantity,
                            newWarehouseIds,
                            newWarehouseStocks
                        ];

                        updateDeletedRowValue(elements, rowNumber);
                    } else {
                        if(rowNumber > 1) {
                            $(`#${i}`).remove();
                        }

                        let deletedElements = [quantity, price, discount];
                        handleDeletedElementAttribute(deletedElements);

                        let elements = [
                            finalAmount,
                            discountProduct,
                            discount,
                            total,
                            price,
                            priceId,
                            unitValue,
                            quantity,
                            realQuantity,
                            warehouseIds,
                            warehouseStocks
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

            function newRowElement(rowId, rowNumber, rowNumbers) {
                return `
                    <tr class="text-bold text-dark" id="${rowId}">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td>
                            <select class="selectpicker sales-order-sku-select-picker" name="product_id[]" id="productId-${rowId}" data-live-search="true" title="Enter SKU" tabindex="${rowNumbers += 1}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="real_quantity[]" id="realQuantity-${rowId}">
                        </td>
                        <td>
                            <select class="selectpicker sales-order-name-select-picker" name="product_name[]" id="productName-${rowId}" data-live-search="true" title="Or Product Name..." tabindex="${rowNumbers += 2}">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-tokens="{{ $product->name }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="warehouse_ids[]" id="warehouseIds-${rowId}">
                            <input type="hidden" name="warehouse_stocks[]" id="warehouseStocks-${rowId}">
                        </td>
                        <td>
                            <input type="text" name="quantity[]" id="quantity-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('quantity[]') }}" tabindex="${rowNumbers += 3}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
                        </td>
                        <td>
                            <select class="selectpicker sales-order-unit-select-picker" name="unit[]" id="unit-${rowId}" data-live-search="true" title="" tabindex="${rowNumbers += 4}" disabled>
                            </select>
                            <input type="hidden" name="unit_id[]" id="unitValue-${rowId}">
                        </td>
                        <td>
                            <select class="selectpicker sales-order-price-type-select-picker" name="price_type[]" id="priceType-${rowId}" data-live-search="true" title="" tabindex="${rowNumbers += 5}" disabled>
                            </select>
                            <input type="hidden" name="price_id[]" id="priceId-${rowId}">
                        </td>
                        <td>
                            <input type="text" name="price[]" id="price-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('price[]') }}" tabindex="${rowNumbers += 6}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly>
                        </td>
                        <td>
                            <input type="text" name="total[]" id="total-${rowId}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('total[]') }}" title="" readonly >
                        </td>
                        <td>
                            <input type="text" name="discount[]" id="discount-${rowId}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ old('discount[]') }}" tabindex="${rowNumbers += 7}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers and plus sign" readonly>
                        </td>
                        <td>
                            <input type="text" name="discount_product[]" id="discountProduct-${rowId}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('discount_product[]') }}" title="" readonly >
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
