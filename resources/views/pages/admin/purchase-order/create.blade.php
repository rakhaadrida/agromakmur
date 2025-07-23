@extends('layouts.admin')

@push('addon-style')
  <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Purchase Order</h1>
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
                            <form action="" id="form">
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
                                        <label for="warehouse" class="col-2 col-form-label text-bold text-right">Warehouse</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <select class="selectpicker warehouse-select-picker" name="warehouse_id" id="warehouse" data-live-search="true" tabindex="3">
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}" data-tokens="{{ $warehouse->name }}">{{ $warehouse->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('warehouse')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <label for="tempo" class="col-1 col-form-label text-bold text-right">Tempo</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-1 mt-1">
                                            <input type="text" tabindex="4" name="tempo" id="tempo" class="form-control form-control-sm text-bold" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers">
                                        </div>
                                        <span class="col-form-label text-bold"> Day(s)</span>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="supplier" class="col-2 col-form-label text-bold text-right">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <select class="selectpicker supplier-select-picker" name="supplier_id" id="supplier" data-live-search="true" tabindex="5">
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
                                        <input type="hidden" name="row_number" id="rowNumber" value="{{ $rowNumbers }}">
                                    </div>
                                </div>
                                <hr>
                                <span class="table-add float-right mb-3 mr-2"><a href="#" class="text-primary text-bold">
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
                                            <td style="width: 50px" class="align-middle">Delete</td>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTable">
                                        @foreach($rows as $key => $row)
                                            <tr class="text-bold text-dark" id="{{ $row }}">
                                                <td class="align-middle text-center">{{ $row }}</td>
                                                <td>
                                                    <select class="selectpicker product-sku-select-picker" name="product_id[]" id="productSku-{{ $key }}" data-live-search="true" title="Enter Product SKU" tabindex="{{ $rowNumbers += 1 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->sku }}">{{ $product->sku }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="primary_unit_id[]" id="primaryUnit-{{ $key }}">
                                                    <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $key }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-name-select-picker" name="product_name[]" id="productName-{{ $key }}" data-live-search="true" title="Or Product Name..." tabindex="{{ $rowNumbers += 2 }}" @if($key == 0) required @endif>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-tokens="{{ $product->name }}">{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="quantity[]" id="quantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right" value="{{ old('quantity[]') }}" tabindex="{{ $rowNumbers += 3 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <select class="selectpicker product-unit-select-picker" name="unit_id[]" id="unit-{{ $key }}" data-live-search="true" title="" tabindex="{{ $rowNumbers += 4 }}" disabled @if($key == 0) required @endif>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="price[]" id="price-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('price[]') }}" tabindex="{{ $rowNumbers += 5 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" readonly @if($key == 0) required @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="total[]" id="total-{{ $key }}" class="form-control-plaintext form-control-sm text-bold text-dark text-right" value="{{ old('total[]') }}" readonly>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="#" class="icRemove" id="deleteRow[]">
                                                        <i class="fas fa-fw fa-times fa-lg ic-remove mt-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                         <button type="submit" tabindex="{{ $rowNumbers++ }}" id="btnSubmit"  class="btn btn-success btn-block text-bold" >Submit</button>
                                    </div>
                                    <div class="col-2">
                                        <button type="reset" tabindex="{{ $rowNumbers++ }}" id="btnReset" class="btn btn-outline-danger btn-block text-bold">Reset</button>
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
                    <h5>There are identical item codes. Please add up the quantities of the same item codes or change the item code.</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalConfirmation" tabindex="-1" role="dialog" aria-labelledby="modalConfirmation" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="h2 text-bold">&times;</span>
                    </button>
                    <h4 class="modal-title">Purchase Order Confirmation</h4>
                </div>
                <div class="modal-body">
                    <p>The purchase order data will be saved. Please select print or re-enter the incoming goods.</p>
                    <hr>
                    <div class="form-row justify-content-center">
                        <div class="col-3">
                            <button type="submit" formaction="#" formmethod="POST" class="btn btn-success btn-block text-bold btnCetak">Print</button>
                        </div>
                        <div class="col-3">
                            <button type="submit" formaction="#" formmethod="POST" class="btn btn-outline-secondary btn-block text-bold">Input Another</button>
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
            let deleteRow = $('a[id="deleteRow[]"]');

            $('select[name="product_id[]"]').each(function (index) {
                $(this).on('change', function (event) {
                    displayPrice(this.value, index);
                });
            });

            $('input[name="price[]"]').each(function(index) {
                $(this).on('keypress', function(event) {
                    if(!this.readOnly) {
                        if (event.which > 31 && (event.which < 48 || event.which > 57)) {
                            $(`#price-${index}`).tooltip('show');
                            event.preventDefault();
                        }
                    }
                });

                $(this).on('keyup', function(event) {
                    this.value = currencyFormat(this.value);
                });

                $(this).on('blur', function(event) {
                    calculateTotal(index);
                });
            });

            $('input[name="quantity[]"]').each(function(index) {
                $(this).on('keypress', function(event) {
                    if(!this.readOnly) {
                        if (event.which > 31 && (event.which < 48 || event.which > 57)) {
                            $(`#price-${index}`).tooltip('show');
                            event.preventDefault();
                        }
                    }
                });

                $(this).on('blur', function(event) {
                    calculateTotal(index);
                });
            });

            $('select[name="unit_id[]"]').each(function (index) {
                $(this).on('change', function (event) {
                    $(`#realQuantity-${index}`).val($(this).find(':selected').data('foo'));

                    calculateTotal(index);
                });
            });

            deleteRow.each(function (index) {
                $(this).on('click', function (event) {
                    console.log(deleteRow.length);
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
                        let quantity = $(`#quantity-${index}`);
                        let productPrice = thousandSeparator(data.main_price);
                        let productUnitId = data.data.unit_id;

                        productName.selectpicker('val', productId);
                        price.val(productPrice);
                        price.attr('readonly', false);
                        quantity.attr('readonly', false);

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
                        });

                        $(`#primaryUnit-${index}`).val(productUnitId);
                        $(`#realQuantity-${index}`).val(1);

                        calculateTotal(index);
                    },
                })
            }

            function calculateTotal(index) {
                let quantity = document.getElementById(`quantity-${index}`);
                let price = document.getElementById(`price-${index}`);
                let total = document.getElementById(`total-${index}`);
                let subtotal = document.getElementById('subtotal');

                let realQuantity = getRealQuantity(quantity.value, index);
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
        });

    </script>
@endpush
