@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Retur - {{ $accountReceivable->number }}</h1>
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
                            <form action="{{ route('account-receivables.update', $accountReceivable->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Nomor SO</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark" name="number" id="number" value="{{ $accountReceivable->number }}" readonly>
                                                    <input type="hidden" name="receivable_id" value="{{ $accountReceivable->id }}">
                                                </div>
                                                <label for="date" class="col-auto col-form-label text-bold">Tanggal SO</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold text-dark" name="date" id="date" value="{{ formatDate($accountReceivable->date, 'd-m-Y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col" style="margin-left: -360px">
                                            <div class="form-group row subtotal-po">
                                                <label for="subtotal" class="col-5 col-form-label text-bold">Sub Total</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2">Rp</span>
                                                <div class="col-5">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-right text-dark" name="subtotal" id="subtotal" value={{ formatPrice($accountReceivable->return_amount) }} readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-top: -15px">
                                        <label for="branch" class="col-2 col-form-label text-bold text-right">Cabang</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 mt-1">
                                            <input type="text" class="form-control form-control-sm text-bold text-dark" name="branch" id="branch" value="{{ $accountReceivable->branch_name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-top: -15px">
                                        <label for="customer" class="col-2 col-form-label text-bold text-right">Customer</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 mt-1">
                                            <input type="text" class="form-control form-control-sm text-bold text-dark" name="customer" id="customer" value="{{ $accountReceivable->customer_name }}" readonly>
                                        </div>
                                        <input type="hidden" name="row_numbers" id="rowNumbers" value="{{ $rowNumbers }}">
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td rowspan="2" class="align-middle table-head-number-sales-order">No</td>
                                            <td rowspan="2" class="align-middle table-head-code-sales-order">SKU</td>
                                            <td rowspan="2" class="align-middle">Nama Produk</td>
                                            <td rowspan="2" class="align-middle th-return-number-receivable-returns">Nomor Retur</td>
                                            <td rowspan="2" class="align-middle th-quantity-receivable-returns">Qty</td>
                                            <td rowspan="2" class="align-middle table-head-unit-sales-order">Unit</td>
                                            <td rowspan="2" class="align-middle table-head-price-type-sales-order">Tipe Harga</td>
                                            <td rowspan="2" class="align-middle th-price-receivable-returns">Harga</td>
                                            <td rowspan="2" class="align-middle th-total-receivable-returns">Total</td>
                                            <td colspan="2" class="align-middle">Diskon</td>
                                            <td rowspan="2" class="align-middle th-final-amount-receivable-returns">Netto</td>
                                        </tr>
                                        <tr>
                                            <td class="th-discount-percentage-receivable-returns">%</td>
                                            <td class="th-discount-amount-receivable-returns">Rupiah</td>
                                        </tr>
                                    </thead>
                                    <tbody class="table-ar" id="itemTable">
                                        @foreach($accountReceivableReturns as $index => $return)
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
                                                    <a href="{{ route('sales-returns.show', $return->sales_return_id) }}" class="btn btn-sm btn-link text-bold text-center">{{ $return->salesReturn->number }}</a>
                                                    <input type="hidden" name="sales_return_id[]" id="salesReturnId-{{ $index }}" value="{{ $return->sales_return_id }}">
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
                                                    <select class="selectpicker sales-order-price-type-select-picker" name="price_type[]" id="priceType-{{ $index }}" data-live-search="true" data-size="6" title="" tabindex="{{ $rowNumbers += 1 }}" required>
                                                        @foreach($prices[$return->product_id] as $price)
                                                            <option value="{{ $price['id'] }}" data-tokens="{{ $price['code'] }}" data-foo="{{ $price['price'] }}" @if($return->price_id == $price['id']) selected @endif>{{ $price['code'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="price_id[]" id="priceId-{{ $index }}" value="{{ $return->price_id }}">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="price[]" id="price-{{ $index }}" value="{{ formatPrice($return->price) }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" required>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="total[]" id="total-{{ $index }}" value="{{ formatPrice($return->total) }}" title="" readonly>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="discount[]" id="discount-{{ $index }}" value="{{ $return->discount }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja and plus sign">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="discount_product[]" id="discountProduct-{{ $index }}" value="{{ formatPrice($return->discount_amount) }}" title="" readonly>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="final_amount[]" id="finalAmount-{{ $index }}" value="{{ formatPrice($return->final_amount) }}" title="" readonly>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr style="font-size: 16px !important">
                                            <td colspan="4" class="align-middle text-center text-bold text-dark">Total</td>
                                            <td class="text-right text-bold text-dark">
                                                <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" id="totalQuantity" value="{{ formatQuantity($accountReceivable->total_quantity ?? 0) }}" title="" style="font-size: 16px" readonly>
                                            </td>
                                            <td colspan="6" class="align-middle text-center text-bold text-dark"></td>
                                            <td class="text-right text-bold text-dark">
                                                <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="total_amount" id="totalAmount" value="{{ formatPrice($accountReceivable->return_amount ?? 0) }}" title="" style="font-size: 16px" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                <div class="form-row justify-content-center">
                                    @if(!isAccountReceivablePaid($accountReceivable->status))
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit">Simpan</button>
                                        </div>
                                    @endif
                                    <div class="col-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Kembali ke Daftar</a>
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

                $('#form').submit();
            });

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
