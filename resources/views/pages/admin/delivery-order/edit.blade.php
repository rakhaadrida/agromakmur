@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Ubah Surat Jalan</h1>
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
                            <form action="{{ route('delivery-orders.update', $deliveryOrder->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="container so-container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-dark text-right">Nomor</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="number" id="number" value="{{ $deliveryOrder->number }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col edit-delivery-general-info-right">
                                            <div class="form-group row so-update-customer">
                                                <label for="invoiceNumber" class="col-4 col-form-label text-bold text-right text-dark">Nomor SO</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6">
                                                    <input type="text" class="form-control-plaintext text-bold text-dark" id="invoiceNumber" value="{{ $deliveryOrder->salesOrder->number }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row do-update-input">
                                                <label for="customer" class="col-4 col-form-label text-bold text-right text-dark">Customer</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-4">
                                                    <input type="text" class="form-control-plaintext text-bold text-dark" id="customer" value="{{ $deliveryOrder->customer->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row do-update-input">
                                                <label for="address" class="col-4 text-right text-bold text-dark mt-2">Alamat</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6">
                                                    <input type="text" class="form-control-plaintext text-bold text-dark" id="address" value="{{ $deliveryOrder->customer->address }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row do-update-input">
                                                <label for="revision" class="col-4 text-right text-bold text-dark mt-2">Revisi</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6">
                                                    <input type="text" class="form-control-plaintext text-bold text-dark" id="revision" value="{{ $deliveryOrder->revision ?? 0 }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row do-update-branch">
                                        <label for="branch" class="col-2 col-form-label text-bold text-dark text-right">Cabang</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="branch" id="branch" value="{{ $deliveryOrder->branch->name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row do-update-date">
                                        <label for="date" class="col-2 col-form-label text-bold text-dark text-right">Tanggal</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ formatDate($deliveryOrder->date, 'd-m-Y') }}" tabindex="3" required>
                                        </div>
                                    </div>
                                    <div class="form-group row so-update-input">
                                        <label for="description" class="col-2 col-form-label text-bold text-dark text-right">Deskripsi</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4">
                                            <input type="text" class="form-control form-control-sm mt-1 text-dark" name="description" id="description" tabindex="1" required autofocus>
                                            <input type="hidden" name="start_date" id="startDate" value="{{ $startDate }}">
                                            <input type="hidden" name="final_date" id="finalDate" value="{{ $finalDate }}">
                                            <input type="hidden" name="row_number" id="rowNumber" value="{{ $rowNumbers }}">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" >
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
                                        @foreach($deliveryOrderItems as $key => $deliveryOrderItem)
                                            <tr class="text-bold text-dark" id="{{ $key }}">
                                                <td class="align-middle text-center">{{ $key + 1 }}</td>
                                                <td class="align-middle">
                                                    <input type="text" name="product_sku[]" id="productSku-{{ $key }}" class="form-control form-control-sm text-bold text-dark readonly-input" title="" value="{{ $deliveryOrderItem->product->sku }}" readonly>
                                                    <input type="hidden" name="product_id[]" id="productId-{{ $key }}" value="{{ $deliveryOrderItem->product_id }}">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" name="product_name[]" id="productName-{{ $key }}" class="form-control form-control-sm text-bold text-dark readonly-input" title="" value="{{ $deliveryOrderItem->product->name }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="order_quantity[]" id="orderQuantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatQuantity($deliveryOrderItem->order_quantity) }}" title="" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="delivered_quantity[]" id="deliveredQuantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatQuantity($deliveryOrderItem->delivered_quantity) }}" title="" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="remaining_quantity[]" id="remainingQuantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatQuantity($deliveryOrderItem->remaining_quantity) }}" title="" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="quantity[]" id="quantity-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-right readonly-input" value="{{ formatQuantity($deliveryOrderItem->quantity) }}" tabindex="{{ $rowNumbers += 7 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" required>
                                                    <input type="hidden" name="real_quantity[]" id="realQuantity-{{ $key }}" value="{{ $deliveryOrderItem->actual_quantity / $deliveryOrderItem->quantity }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" name="unit[]" id="unit-{{ $key }}" class="form-control form-control-sm text-bold text-dark text-center readonly-input" title="" value="{{ $deliveryOrderItem->unit->name }}" readonly>
                                                    <input type="hidden" name="unit_id[]" id="unitId-{{ $key }}" value="{{ $deliveryOrderItem->unit_id }}">
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
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        let url = new URL(window.location.href);
        let params = url.searchParams;

        params.delete('start_date');
        params.delete('final_date');
        params.delete('number');
        params.delete('customer_id');

        window.history.pushState({}, document.title, url.toString());

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
                    $('#form').submit();
                }
            });

            function currencyFormat(value) {
                return value
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    ;
            }

            function numberFormat(value) {
                return +value.replace(/\./g, "");
            }
        });
    </script>
@endpush
