@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Account Receivable Return - {{ $accountReceivable->number }}</h1>
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
                            <form action="{{ route('account-receivables.store') }}" method="POST" id="form">
                                @csrf
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Order Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark" name="number" id="number" value="{{ $accountReceivable->number }}" readonly>
                                                    <input type="hidden" name="receivable_id" value="{{ $accountReceivable->id }}">
                                                </div>
                                                <label for="date" class="col-auto col-form-label text-bold">Order Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold text-dark" name="date" id="date" value="{{ formatDate($accountReceivable->date, 'd-m-Y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col" style="margin-left: -360px">
                                            <div class="form-group row subtotal-po">
                                                <label for="grandTotal" class="col-5 col-form-label text-bold">Sub Total</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2">Rp</span>
                                                <div class="col-5">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-right text-dark" name="grand_total" id="grandTotal" value={{ formatPrice($accountReceivable->grand_total) }} readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-top: -7px">
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
                                            <td rowspan="2" class="align-middle">Product Name</td>
                                            <td rowspan="2" class="align-middle">Return Number</td>
                                            <td rowspan="2" class="align-middle table-head-quantity-sales-order">Qty</td>
                                            <td rowspan="2" class="align-middle table-head-unit-sales-order">Unit</td>
                                            <td rowspan="2" class="align-middle table-head-price-type-sales-order">Price Type</td>
                                            <td rowspan="2" class="align-middle table-head-price-sales-order">Price</td>
                                            <td rowspan="2" class="align-middle table-head-total-sales-order">Total</td>
                                            <td colspan="2" class="align-middle">Discount</td>
                                            <td rowspan="2" class="align-middle table-head-final-amount-sales-order">Final Amount</td>
                                        </tr>
                                        <tr>
                                            <td class="table-head-discount-percentage-sales-order">%</td>
                                            <td class="table-head-discount-amount-sales-order">Rupiah</td>
                                        </tr>
                                    </thead>
                                    <tbody class="table-ar" id="itemTable">
                                        @foreach($accountReceivableReturns as $index => $return)
                                            <tr class="table-modal-first-row text-dark" id="{{ $index }}">
                                                <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_sku[]" id="productSku-{{ $index }}" value="{{ $return->product->sku }}" title="" readonly>
                                                    <input type="hidden" name="product_id[]" id="productId-{{ $index }}" value="{{ $return->product_id }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_name[]" id="productName-{{ $index }}" value="{{ $return->product->name }}" title="" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="return_number[]" id="returnNumber-{{ $index }}" value="{{ $return->salesReturn->number }}" title="" readonly>
                                                    <input type="hidden" name="sales_return_id[]" id="salesReturnId-{{ $index }}" value="{{ $return->sales_return_id }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="quantity[]" id="quantity-{{ $index }}" value="{{ formatQuantity($return->quantity) }}" title="" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="unit_name[]" id="unitName-{{ $index }}" value="{{ $return->unit->name }}" title="" readonly>
                                                    <input type="hidden" name="unit_id[]" id="unitId-{{ $index }}" value="{{ $return->unit_id }}">
                                                </td>
                                                <td>
                                                    <select class="selectpicker sales-order-price-type-select-picker" name="price_type[]" id="priceType-{{ $index }}" data-live-search="true" data-size="6" title="" tabindex="{{ $rowNumbers += 1 }}" required>
                                                        @foreach($prices[$return->product_id] as $price)
                                                            <option value="{{ $price['id'] }}" data-tokens="{{ $price['code'] }}" data-foo="{{ $price['price'] }}" @if($return->price_id == $price['id']) selected @endif>{{ $price['code'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="price_id[]" id="priceId-{{ $index }}" value="{{ $return->price_id }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="price[]" id="price-{{ $index }}" value="{{ $return->price }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="total[]" id="total-{{ $index }}" value="{{ $return->total }}" title="" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="discount[]" id="discount-{{ $index }}" value="{{ $return->discount }}" tabindex="{{ $rowNumbers += 1 }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers and plus sign" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="discount_product[]" id="discountProduct-{{ $index }}" value="{{ $return->discount_amount }}" title="" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="final_amount[]" id="finalAmount-{{ $index }}" value="{{ $return->final_amount }}" title="" readonly>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr style="font-size: 16px !important">
                                            <td colspan="4" class="align-middle text-center text-bold text-dark">Total</td>
                                            <td class="text-right text-bold text-dark">
                                                <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" id="totalQuantity" value="{{ $accountReceivable->total_quantity ?? 0 }}" title="" style="font-size: 16px" readonly>
                                            </td>
                                            <td colspan="6" class="align-middle text-center text-bold text-dark"></td>
                                            <td class="text-right text-bold text-dark">
                                                <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="grand_total" id="grandTotal" value="{{ $accountReceivable->return_amount ?? 0 }}" title="" style="font-size: 16px" readonly>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                <div class="form-row justify-content-center">
                                    @if(!isAccountReceivablePaid($accountReceivable->status))
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
    <script type="text/javascript">
        $.fn.datepicker.dates['id'] = {
            days:["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"],
            daysShort:["Mgu","Sen","Sel","Rab","Kam","Jum","Sab"],
            daysMin:["Min","Sen","Sel","Rab","Kam","Jum","Sab"],
            months:["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"],
            monthsShort:["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"],
            today:"Hari Ini",
            clear:"Kosongkan"
        };

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'id',
        });

        $(document).ready(function() {
            const table = $('#itemTable');
            let totalOutstanding = document.getElementById(`outstandingAmount`);

            table.on('change', 'input[name="payment_date[]"]', function () {
                const index = $(this).closest('tr').index();

                if(this.value !== '') {
                    $(`#paymentAmount-${index}`).attr('required', true);
                } else {
                    $(`#paymentAmount-${index}`).removeAttr('required');
                }
            });

            table.on('keypress', 'input[name="payment_amount[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();
                    $(`#paymentAmount-${index}`).tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="payment_amount[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            table.on('blur', 'input[name="payment_amount[]"]', function () {
                const index = $(this).closest('tr').index();
                calculateOutstandingAmount(index);

                if(this.value !== '') {
                    $(`#paymentDate-${index}`).attr('required', true);
                    $(`#paymentAmount-${index}`).attr('required', true);
                } else {
                    $(`#paymentDate-${index}`).removeAttr('required');
                    $(`#paymentAmount-${index}`).removeAttr('required');
                    $(`#outstandingPayment-${index}`).val('');
                }
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

                let finalOutstandingAmount = $('#finalOutstandingPayment').val();

                if(numberFormat(finalOutstandingAmount) < 0) {
                    let outstandingAmount = $('#outstandingAmount').val()
                    $(`#totalOutstanding`).text(`${thousandSeparator(outstandingAmount)}`);
                    $('#modalNotification').modal('show');
                } else {
                    $('input[name="payment_amount[]"]').each(function() {
                        this.value = numberFormat(this.value);
                    });

                    $('#form').submit();
                }
            });

            function calculateOutstandingAmount(index) {
                let previousIndex = index - 1;
                let previousOutstanding = document.getElementById(`outstandingPayment-${previousIndex}`) ?? totalOutstanding;
                let currentOutstanding = document.getElementById(`outstandingPayment-${index}`);
                let paymentAmount = document.getElementById(`paymentAmount-${index}`);
                let basePaymentAmount = document.getElementById(`basePaymentAmount-${index}`);
                let finalPayment = document.getElementById('finalPaymentAmount');

                let outstandingAmount = numberFormat(previousOutstanding.value) - numberFormat(paymentAmount.value);
                let totalPaymentAmount = numberFormat(basePaymentAmount.value) - numberFormat(paymentAmount.value);
                let finalPaymentAmount = numberFormat(finalPayment.value) - +totalPaymentAmount;

                basePaymentAmount.value = paymentAmount.value;
                finalPayment.value = thousandSeparator(finalPaymentAmount);
                currentOutstanding.value = thousandSeparator(outstandingAmount);
                $('#finalOutstandingPayment').val(thousandSeparator(outstandingAmount));
            }

            function updateAllRowIndexes(index, deleteRow) {
                let finalPayment = document.getElementById('finalPaymentAmount');
                let finalOutstanding = document.getElementById('finalOutstandingPayment');
                let currentPaymentAmount = document.getElementById(`paymentAmount-${index}`);

                if(currentPaymentAmount.value !== '') {
                    let finalPaymentAmount = numberFormat(finalPayment.value) - numberFormat(currentPaymentAmount.value);
                    let finalOutstandingAmount = numberFormat(finalOutstanding.value) + numberFormat(currentPaymentAmount.value);

                    finalOutstanding.value = thousandSeparator(finalOutstandingAmount);
                    finalPayment.value = thousandSeparator(finalPaymentAmount);
                }

                for(let i = index; i < deleteRow.length; i++) {
                    let previousIndex = i - 1;
                    let previousOutstanding = document.getElementById(`outstandingPayment-${previousIndex}`) ?? totalOutstanding;

                    let paymentDate = document.getElementById(`paymentDate-${i}`);
                    let paymentAmount = document.getElementById(`paymentAmount-${i}`);
                    let basePaymentAmount = document.getElementById(`basePaymentAmount-${i}`);
                    let outstandingPayment = document.getElementById(`outstandingPayment-${i}`);

                    let rowNumber = +i + 1;
                    let newPaymentDate = document.getElementById(`paymentDate-${rowNumber}`);
                    let newPaymentAmount = document.getElementById(`paymentAmount-${rowNumber}`);
                    let newBasePaymentAmount = document.getElementById(`basePaymentAmount-${rowNumber}`);
                    let newOutstandingPayment = document.getElementById(`outstandingPayment-${rowNumber}`);

                    if(rowNumber !== deleteRow.length) {
                        let newOutstandingAmount = numberFormat(previousOutstanding.value) - numberFormat(newPaymentAmount.value);

                        paymentDate.placeholder = newPaymentDate.placeholder;
                        paymentDate.value = newPaymentDate.value;
                        paymentAmount.value = newPaymentAmount.value;
                        basePaymentAmount.value = newBasePaymentAmount.value;
                        outstandingPayment.value = newOutstandingPayment.value;

                        if(newOutstandingPayment.value !== '') {
                            outstandingPayment.value = thousandSeparator(newOutstandingAmount);
                        }

                        if(newPaymentDate.value === '') {
                            handleDeletedElement(paymentDate, paymentAmount);
                            updateDeletedRowValue([], i);
                        } else {
                            newPaymentDate.removeAttribute('required');
                            newPaymentAmount.removeAttribute('required');
                        }

                        let elements = [newPaymentDate, newPaymentAmount, newBasePaymentAmount, newOutstandingPayment];
                        updateDeletedRowValue(elements, rowNumber);
                    } else {
                        handleDeletedElement(paymentDate, paymentAmount);

                        let elements = [paymentDate, paymentAmount, basePaymentAmount, outstandingPayment];
                        updateDeletedRowValue(elements, i);
                    }
                }

                if(index !== deleteRow.length - 1) {
                    $(`#${deleteRow.length - 1}`).remove();
                }
            }

            function handleDeletedElement(paymentDate, paymentAmount) {
                paymentDate.removeAttribute('required');
                paymentAmount.removeAttribute('required');
            }

            function updateDeletedRowValue(elements, index) {
                elements.forEach(function(element) {
                    element.value = '';
                });
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
