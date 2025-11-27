@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Account Payable Return - {{ $accountPayable->number }}</h1>
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
                            <form action="{{ route('account-payables.update', $accountPayable->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Receipt Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark" name="number" id="number" value="{{ $accountPayable->number }}" readonly>
                                                    <input type="hidden" name="payable_id" value="{{ $accountPayable->id }}">
                                                </div>
                                                <label for="date" class="col-auto col-form-label text-bold">Receipt Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold text-dark" name="date" id="date" value="{{ formatDate($accountPayable->date, 'd-m-Y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col" style="margin-left: -360px">
                                            <div class="form-group row subtotal-po">
                                                <label for="subtotal" class="col-5 col-form-label text-bold">Sub Total</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2">Rp</span>
                                                <div class="col-5">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-right text-dark" name="subtotal" id="subtotal" value={{ formatPrice($accountPayable->return_amount) }} readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-top: -15px">
                                        <label for="branch" class="col-2 col-form-label text-bold text-right">Branch</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 mt-1">
                                            <input type="text" class="form-control form-control-sm text-bold text-dark" name="branch" id="branch" value="{{ $accountPayable->branch_name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-top: -15px">
                                        <label for="customer" class="col-2 col-form-label text-bold text-right">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 mt-1">
                                            <input type="text" class="form-control form-control-sm text-bold text-dark" name="customer" id="customer" value="{{ $accountPayable->supplier_name }}" readonly>
                                        </div>
                                        <input type="hidden" name="row_numbers" id="rowNumbers" value="{{ $rowNumbers }}">
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td class="align-middle table-head-number-sales-order">No</td>
                                            <td class="align-middle table-head-code-sales-order">SKU</td>
                                            <td class="align-middle">Product Name</td>
                                            <td class="align-middle th-return-number-receivable-returns">Return Number</td>
                                            <td class="align-middle th-quantity-receivable-returns">Qty</td>
                                            <td class="align-middle table-head-unit-sales-order">Unit</td>
                                            <td class="align-middle th-price-receivable-returns">Price</td>
                                            <td class="align-middle th-price-receivable-returns">Wages</td>
                                            <td class="align-middle th-price-receivable-returns">Shipping Cost</td>
                                            <td class="align-middle th-total-receivable-returns">Total</td>
                                        </tr>
                                    </thead>
                                    <tbody class="table-ar" id="itemTable">
                                        @foreach($accountPayableReturns as $index => $return)
                                            <tr class="table-modal-first-row text-dark" id="{{ $index }}">
                                                <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_sku[]" id="productSku-{{ $index }}" value="{{ $return->product->sku }}" title="" readonly>
                                                    <input type="hidden" name="product_id[]" id="productId-{{ $index }}" value="{{ $return->product_id }}">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_name[]" id="productName-{{ $index }}" value="{{ $return->product->name }}" title="" readonly>
                                                </td>
                                                <td class="align-middle">
                                                    <a href="{{ route('purchase-returns.show', $return->purchase_return_id) }}" class="btn btn-sm btn-link text-bold text-center">{{ $return->purchaseReturn->number }}</a>
                                                    <input type="hidden" name="purchase_return_id[]" id="purchaseReturnId-{{ $index }}" value="{{ $return->purchase_return_id }}">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="quantity[]" id="quantity-{{ $index }}" value="{{ formatQuantity($return->quantity) }}" title="" readonly>
                                                    <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $index }}" value="{{ $return->actual_quantity / $return->quantity }}">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-center readonly-input" name="unit_name[]" id="unitName-{{ $index }}" value="{{ $return->unit->name }}" title="" readonly>
                                                    <input type="hidden" name="unit_id[]" id="unitId-{{ $index }}" value="{{ $return->unit_id }}">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="price[]" id="price-{{ $index }}" value="{{ formatPrice($return->price) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" required>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="wages[]" id="wages-{{ $index }}" value="{{ formatPrice($return->wages) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="shipping_cost[]" id="shippingCost-{{ $index }}" value="{{ formatPrice($return->shipping_cost) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="total[]" id="total-{{ $index }}" value="{{ formatPrice($return->total) }}" title="" readonly>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr style="font-size: 16px !important">
                                            <td colspan="4" class="align-middle text-center text-bold text-dark">Total</td>
                                            <td class="text-right text-bold text-dark">
                                                <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" id="totalQuantity" value="{{ formatQuantity($accountPayable->total_quantity ?? 0) }}" title="" style="font-size: 16px" readonly>
                                            </td>
                                            <td colspan="4" class="align-middle text-center text-bold text-dark"></td>
                                            <td class="text-right text-bold text-dark">
                                                <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="total_amount" id="totalAmount" value="{{ formatPrice($accountPayable->return_amount ?? 0) }}" title="" style="font-size: 16px" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                <div class="form-row justify-content-center">
                                    @if(!isAccountPayablePaid($accountPayable->status))
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit">Submit</button>
                                        </div>
                                    @endif
                                    <div class="col-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Back to List</a>
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
        $(document).ready(function() {
            const table = $('#itemTable');
            let subtotal = document.getElementById('subtotal');
            let totalAmount = document.getElementById('totalAmount');

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

                $('input[name="wages[]"]').each(function() {
                    this.value = numberFormat(this.value);
                });

                $('input[name="shipping_cost[]"]').each(function() {
                    this.value = numberFormat(this.value);
                });

                $('#form').submit();
            });

            function calculateTotal(index) {
                let quantity = document.getElementById(`quantity-${index}`);
                let price = document.getElementById(`price-${index}`);
                let wages = document.getElementById(`wages-${index}`);
                let shippingCost = document.getElementById(`shippingCost-${index}`);
                let total = document.getElementById(`total-${index}`);

                let realQuantity = getRealQuantity(numberFormat(quantity.value), index);
                let currentTotal = 0;

                if(quantity.value === "") {
                    totalAmount.value = thousandSeparator(numberFormat(totalAmount.value) - numberFormat(total.value));
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
            }

            function getRealQuantity(quantity, index) {
                let realQuantity = $(`#realQuantity-${index}`).val();

                return +quantity * +realQuantity;
            }

            function calculateSubtotal(previousAmount, currentAmount, subtotal, total) {
                if(previousAmount > currentAmount) {
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) - (+previousAmount - +currentAmount));
                    totalAmount.value = thousandSeparator(numberFormat(totalAmount.value) - (+previousAmount - +currentAmount));
                } else {
                    subtotal.value = thousandSeparator(numberFormat(subtotal.value) + (+currentAmount - +previousAmount));
                    totalAmount.value = thousandSeparator(numberFormat(totalAmount.value) + (+currentAmount - +previousAmount));
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
