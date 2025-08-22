@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Account Payable - {{ $accountPayable->number }}</h1>
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
                            <form action="{{ route('account-payables.store') }}" method="POST" id="form">
                                @csrf
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
                                                <label for="grandTotal" class="col-5 col-form-label text-bold">Grand Total</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2">Rp</span>
                                                <div class="col-5">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-right text-dark" name="grand_total" id="grandTotal" value={{ formatPrice($accountPayable->grand_total) }} readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row" style="margin-top: -25px">
                                                <label for="returnAmount" class="col-5 col-form-label text-bold">Return Amount</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2">Rp</span>
                                                <div class="col-5">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-right text-dark" name="return_amount" id="returnAmount" value="0" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row" style="margin-top: -25px">
                                                <label for="outstandingAmount" class="col-5 col-form-label text-bold">Outstanding</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <span class="col-form-label text-bold ml-2">Rp</span>
                                                <div class="col-5">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-right text-dark" name="outstanding_amount" id="outstandingAmount" value={{ formatPrice($accountPayable->grand_total) }} readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-top: -60px">
                                        <label for="supplier" class="col-2 col-form-label text-bold text-right">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 mt-1">
                                            <input type="text" readonly class="form-control form-control-sm text-bold text-dark" name="supplier" id="supplier" value="{{ $accountPayable->supplier_name }}">
                                        </div>
                                        <input type="hidden" name="row_numbers" id="rowNumbers" value="{{ $rowNumbers }}">
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                    <thead class="text-center text-bold text-dark">
                                        <tr class="text-center">
                                            <th style="width: 60px">No</th>
                                            <th style="width: 160px">Payment Date</th>
                                            <th style="width: 160px">Payment Amount</th>
                                            <th style="width: 160px">Outstanding Amount</th>
                                            <th style="width: 60px">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-ar" id="itemTable" >
                                        @foreach($accountPayablePayments as $index => $accountPayablePayment)
                                            <tr class="table-modal-first-row text-dark" id="{{ $index }}" style="font-size: 16px !important">
                                                <td class="text-center align-middle">{{ $index + 1 }}</td>
                                                <td class="text-center">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold text-dark text-center" name="payment_date[]" id="paymentDate-{{ $index }}" value="{{ formatDate($accountPayablePayment->date, 'd-m-Y') }}" title="" style="font-size: 16px">
                                                </td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right" name="payment_amount[]" id="paymentAmount-{{ $index }}" value="{{ formatPrice($accountPayablePayment->amount) }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" style="font-size: 16px">
                                                    <input type="hidden" name="base_payment_amount[]" id="basePaymentAmount-{{ $index }}" value="{{ $accountPayablePayment->amount }}">
                                                </td>
                                                <td class="text-right align-middle text-bold">
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="outstanding_payment[]" id="outstandingPayment-{{ $index }}" value="{{ formatPrice($accountPayablePayment->outstanding_amount) }}" title="" style="font-size: 16px" readonly>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <button type="button" class="remove-transaction-table" id="deleteRow[]">
                                                        <i class="fas fa-fw fa-times fa-lg ic-remove mt-1"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if(!isAccountPayablePaid($accountPayable->status))
                                            <tr class="text-dark" id="{{ $rowNumbers }}">
                                                <td class="text-center align-middle">{{ $rowNumbers + 1 }}</td>
                                                <td class="text-center align-middle">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold text-dark text-center" name="payment_date[]" id="paymentDate-{{ $rowNumbers }}" title="" placeholder="DD-MM-YYYY" style="font-size: 16px" @if(!$rowNumbers) required @endif>
                                               </td>
                                                <td class="text-right align-middle">
                                                    <input type="text" class="form-control form-control-sm text-bold text-dark text-right" name="payment_amount[]" id="paymentAmount-{{ $rowNumbers }}" title="" style="font-size: 16px" @if(!$rowNumbers) required @endif>
                                                    <input type="hidden" name="base_payment_amount[]" id="basePaymentAmount-{{ $rowNumbers }}" value="0">
                                                </td>
                                                <td class="text-right align-middle">
                                                    <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="outstanding_payment[]" id="outstandingPayment-{{ $rowNumbers }}" title="" style="font-size: 16px" readonly>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <button type="button" class="remove-transaction-table" id="deleteRow[]">
                                                        <i class="fas fa-fw fa-times fa-lg ic-remove mt-1"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr style="font-size: 16px !important">
                                            <td colspan="2" class="align-middle text-center text-bold text-dark" >Total</td>
                                            <td class="text-right text-bold text-dark">
                                                <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" id="finalPaymentAmount" value="{{ formatPrice($accountPayable->payment_amount ?? 0) }}" title="" style="font-size: 16px" readonly>
                                            </td>
                                            <td class="text-right text-bold text-dark">
                                                <input type="text" class="form-control-plaintext form-control-sm text-bold text-dark text-right" name="final_outstanding_payment" id="finalOutstandingPayment" value="{{ formatPrice($accountPayable->outstanding_amount) }}" title="" style="font-size: 16px" readonly>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                @if(!isAccountPayablePaid($accountPayable->status))
                                    <div class="form-row justify-content-center">
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit">Submit</button>
                                        </div>
                                        <div class="col-2">
                                            <button type="reset" class="btn btn-outline-danger btn-block text-bold">Reset</button>
                                        </div>
                                        <div class="col-2">
                                            <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Back to List</a>
                                        </div>
                                    </div>
                                @endif

                                <div class="modal" id="modalNotification" tabindex="-1" role="dialog" aria-labelledby="modalNotification" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" class="h2 text-bold">&times;</span>
                                                </button>
                                                <h4 class="modal-title text-bold">Payment Amount Notification</h4>
                                            </div>
                                            <div class="modal-body text-dark">
                                                <h5>The installment amount cannot exceed the outstanding amount. Total outstanding for the invoice is <span class="text-bold" id="totalOutstanding"></span></h5>
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
