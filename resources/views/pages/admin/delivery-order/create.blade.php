@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Surat Jalan</h1>
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
                            <form action="{{ route('delivery-orders.store') }}" method="POST" id="form">
                                @csrf
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Nomor Surat Jalan</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold" name="number" id="number" value="{{ old('number') }}" data-old-value="{{ old('number') }}" tabindex="1" disabled required>
                                                </div>
                                                <label for="date" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Tanggal Kirim</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ $date }}" tabindex="2" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row delivery-order-customer-input">
                                        <label for="salesOrderId" class="col-2 col-form-label text-bold text-right">Nomor SO</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <select class="selectpicker warehouse-select-picker" name="sales_order_id" id="salesOrderId" data-live-search="true" data-size="6" data-size="5" title="Input atau Pilih Nomor" tabindex="3" autofocus required>
                                                @foreach($salesOrders as $salesOrder)
                                                    <option value="{{ $salesOrder->id }}" data-tokens="{{ $salesOrder->number }}">{{ $salesOrder->number }}</option>
                                                @endforeach
                                            </select>
                                            @error('sales_order_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <label for="branch" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Cabang</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control form-control-sm text-bold" name="branch" id="branch" tabindex="2" readonly>
                                            <input type="hidden" name="branch_id" id="branchId">
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="customer" class="col-2 col-form-label text-bold text-right">Customer</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <input type="text" name="customer" id="customer" class="form-control form-control-sm text-bold" readonly>
                                            <input type="hidden" name="customer_id" id="customerId">
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="address" class="col-2 col-form-label text-bold text-right">Alamat</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-6 mt-1">
                                            <input type="text" class="form-control form-control-sm text-bold" name="address" id="address" value="{{ old('address') }}" tabindex="4" required>
                                        </div>
                                        <input type="hidden" name="row_number" id="rowNumber" value="{{ $rowNumbers }}">
                                        <input type="hidden" name="is_generated_number" id="isGeneratedNumber" value="1">
                                    </div>
                                </div>
                                <hr>
                                <div id="itemContent" hidden>
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="align-middle table-head-number-delivery-order">No</td>
                                                <td class="align-middle table-head-code-delivery-order">SKU</td>
                                                <td class="align-middle">Nama Produk</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Qty Order</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Qty Terkirim</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Sisa Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Qty Dikirim</td>
                                                <td class="align-middle table-head-unit-delivery-order">Unit</td>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTable">
                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="form-row justify-content-center">
                                        <div class="col-2">
                                             <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit" tabindex="10000">Simpan</button>
                                        </div>
                                        <div class="col-2">
                                            <button type="reset" class="btn btn-outline-danger btn-block text-bold" id="btnReset" tabindex="10001">Reset</button>
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
                                                <h4 class="modal-title">Konfirmasi Surat Jalan</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>Data Surat Jalan akan disimpan. Silakan pilih cetak atau input kembali Surat Jalan.</p>
                                                <input type="hidden" name="is_print" value="0">
                                                <hr>
                                                <div class="form-row justify-content-center">
                                                    <div class="col-3">
                                                        <button type="button" class="btn btn-success btn-block text-bold" id="btnPrint">Cetak</button>
                                                    </div>
                                                    <div class="col-4">
                                                        <button type="submit" class="btn btn-outline-secondary btn-block text-bold">Input Lagi</button>
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

            $('#salesOrderId').change(function() {
                $('#itemContent').removeAttr('hidden');

                let salesOrderId = $(this).val();
                displaySalesOrderData(salesOrderId);
            });

            $('#branchId').change(function() {
                generateAutoNumber($(this).val());
            });

            $('#number').on('blur', function(event) {
                event.preventDefault();

                let oldValue = $(this).data('old-value');
                let currentValue = this.value;

                if(oldValue !== currentValue) {
                    $('#isGeneratedNumber').val(0);
                } else {
                    $('#isGeneratedNumber').val(1);
                }
            });

            table.on('keypress', 'input[name="quantity[]"]', function (event) {
                if (!this.readOnly && event.which > 31 && (event.which < 48 || event.which > 57)) {
                    const index = $(this).closest('tr').index();

                    let quantity = $(`#quantity-${index}`);
                    quantity.attr('title', 'Hanya masukkan angka saja');
                    quantity.attr('data-original-title', 'Hanya masukkan angka saja');
                    quantity.tooltip('show');

                    event.preventDefault();
                }
            });

            table.on('keyup', 'input[name="quantity[]"]', function () {
                this.value = currencyFormat(this.value);
            });

            $('#btnSubmit').on('click', function(event) {
                event.preventDefault();

                let checkForm = document.getElementById('form').checkValidity();
                if(!checkForm) {
                    document.getElementById('form').reportValidity();
                    return false;
                }

                let isInvalidQuantity = 0;
                $('input[name="quantity[]"]').each(function(index) {
                    this.value = numberFormat(this.value);

                    let remainingQuantityElement = $(`#remainingQuantity-${index}`);
                    let remainingQuantity = numberFormat(remainingQuantityElement.val());

                    if(this.value > remainingQuantity) {
                        let quantity = $(`#quantity-${index}`);
                        quantity.attr('title', 'Quantity to be sent can not greater than remaining quantity');
                        quantity.attr('data-original-title', 'Quantity to be sent can not greater than remaining quantity');
                        quantity.tooltip('show');
                        isInvalidQuantity = 1;

                        return false;
                    }
                });

                if(!isInvalidQuantity) {
                    $('input[name="order_quantity[]"]').each(function() {
                        this.value = numberFormat(this.value);
                    });

                    $('#modalConfirmation').modal('show');
                }
            });

            $('#btnPrint').on('click', function(event) {
                event.preventDefault();

                $('input[name="is_print"]').val(1);
                $('#form').submit();
            });

            function displaySalesOrderData(salesOrderId) {
                $.ajax({
                    url: '{{ route('sales-orders.index-ajax') }}',
                    type: 'GET',
                    data: {
                        sales_order_id: salesOrderId,
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#branchId').val(data.data.branch_id).trigger('change');
                        $('#branch').val(data.data.branch_name);
                        $('#customerId').val(data.data.customer_id);
                        $('#customer').val(data.data.customer_name);
                        $('#address').val(data.data.customer_address);

                        let salesOrderItems = data.sales_order_items;
                        let rowId = 0;
                        let rowNumber = 1;
                        let rowNumbers = 5;

                        table.empty();
                        $.each(salesOrderItems, function(index, item) {
                            let newRow = salesOrderItemRowElement(rowId, rowNumber, rowNumbers, item);
                            table.append(newRow);

                            rowId++;
                            rowNumber++;
                            rowNumbers++;
                        });
                    },
                })
            }

            function generateAutoNumber(branchId) {
                $.ajax({
                    url: '{{ route('delivery-orders.generate-number-ajax') }}',
                    type: 'GET',
                    data: {
                        branch_id: branchId,
                    },
                    dataType: 'json',
                    success: function(data) {
                        let number = $('#number');

                        number.val(data.number);
                        number.data('old-value', data.number);
                        number.removeAttr('disabled');
                    },
                })
            }

            function salesOrderItemRowElement(rowId, rowNumber, rowNumbers, item) {
                return `
                    <tr class="text-bold text-dark" id="${rowId}">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_sku[]" id="productSku-${rowId}" value="${item.product_sku}" title="" readonly>
                            <input type="hidden" name="product_id[]" id="productId-${rowId}" value="${item.product_id}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_name[]" id="productName-${rowId}" value="${item.product_name}" title="" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="order_quantity[]" id="orderQuantity-${rowId}" value="${thousandSeparator(item.quantity)}" title="" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="delivered_quantity[]" id="deliveredQuantity-${rowId}" value="${thousandSeparator(item.delivered_quantity)}" title="" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="remaining_quantity[]" id="remainingQuantity-${rowId}" value="${thousandSeparator(item.remaining_quantity)}" title="" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="quantity[]" id="quantity-${rowId}" value="${thousandSeparator(item.remaining_quantity)}" tabindex="${rowNumbers += 1}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" required>
                            <input type="hidden" name="real_quantity[]" id="realQuantity-${rowId}" value="${item.actual_quantity}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-bold text-dark text-center readonly-input" name="unit[]" id="unit-${rowId}" value="${item.unit_name}" title="" readonly>
                            <input type="hidden" name="unit_id[]" id="unitId-${rowId}" value="${item.unit_id}">
                        </td>
                    </tr>
                `;
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
