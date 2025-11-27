@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Ubah Sales Order</h1>
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
                            <form action="{{ route('sales-orders.index-edit') }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row">
                                        <label for="number" class="col-2 col-form-label text-bold text-right">Nomor Order</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control form-control-sm text-bold mt-1" name="number" id="number" value="{{ $number }}" tabindex="1" autofocus>
                                        </div>
                                        <label for="supplier" class="col-auto col-form-label text-bold text-right filter-supplier-receipt">Customer</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4">
                                            <select class="selectpicker supplier-params-select-picker" name="customer_id" id="customer" data-live-search="true" data-size="6" title="Input atau Pilih Customer" tabindex="2">
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" data-tokens="{{ $customer->name }}" @if($customerId == $customer->id) selected @endif>{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row filter-date-receipt">
                                        <label for="startDate" class="col-2 col-form-label text-bold text-right">Tanggal Awal</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" tabindex="3">
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-bold text-right filter-final-date-receipt">Tanggal Akhir</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}" tabindex="4">
                                        </div>
                                        <div class="col-1 mt-1 btn-search-receipt">
                                            <button type="submit" id="btnSearch" class="btn btn-primary btn-sm btn-block text-bold" tabindex="5">Cari</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="so-carousel" class="carousel slide" data-interval="false" wrap="false">
                                    <div class="carousel-inner">
                                        @forelse($salesOrders as $key => $salesOrder)
                                            <div class="carousel-item @if(!$key) active @endif">
                                                <div class="container so-update-container text-dark">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <label for="orderNumber" class="col-2 form-control-sm text-bold text-right mt-1">Nomor Order</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-2">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="orderNumber" value="{{ $salesOrder->number }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col edit-receipt-general-info-right">
                                                            <div class="form-group row">
                                                                <label for="branch" class="col-3 form-control-sm text-bold text-right mt-1">Cabang</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-4">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="branch" value="{{ $salesOrder->branch_name }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row customer-detail">
                                                            <label for="date" class="col-2 form-control-sm text-bold text-right mt-1">Tanggal Order</label>
                                                            <span class="col-form-label text-bold">:</span>
                                                            <div class="col-2">
                                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="date" value="{{ formatDate($salesOrder->date, 'd-m-Y') }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col edit-receipt-general-info-right">
                                                        <div class="form-group row customer-detail">
                                                            <label for="customer" class="col-3 form-control-sm text-bold text-right mt-1">Customer</label>
                                                            <span class="col-form-label text-bold">:</span>
                                                            <div class="col-4">
                                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="customer" value="{{ $salesOrder->customer_name }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group row customer-detail">
                                                                <label for="dueDate" class="col-2 form-control-sm text-bold text-right mt-1">Jatuh Tempo</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-4">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark text-wrap" id="dueDate" value="{{ getDueDate($salesOrder->date, $salesOrder->tempo, 'd-m-Y') }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col edit-receipt-general-info-right">
                                                            <div class="form-group row customer-detail">
                                                                <label for="marketing" class="col-3 form-control-sm text-bold text-right mt-1">Sales</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-8">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark text-wrap" id="marketing" value="{{ $salesOrder->marketing_name }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group row customer-detail">
                                                                <label for="user" class="col-2 form-control-sm text-bold text-right mt-1">Admin</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-3">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="user" value="{{ $salesOrder->user_name }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col edit-receipt-general-info-right">
                                                            <div class="form-group row customer-detail">
                                                                <label for="status" class="col-3 form-control-sm text-bold text-right mt-1">Status</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-6">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="status" value="{{ getSalesOrderStatusLabel($salesOrder->status) }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group row customer-detail">
                                                                <label for="note" class="col-2 form-control-sm text-bold text-right mt-1">Note</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-3">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="note" value="{{ $salesOrder->note }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if(isUpdated($salesOrder->status))
                                                            <div class="col edit-receipt-general-info-right">
                                                                <div class="form-group row customer-detail">
                                                                    <label for="revision" class="col-3 form-control-sm text-bold text-right mt-1">Revisi</label>
                                                                    <span class="col-form-label text-bold">:</span>
                                                                    <div class="col-6">
                                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="revision" value="{{ $mapRevisionBySalesOrderId[$salesOrder->id] }}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @elseif(isWaitingApproval($salesOrder->status))
                                                            <div class="col edit-receipt-general-info-right">
                                                                <div class="form-group row customer-detail">
                                                                    <label for="approvalType" class="col-3 form-control-sm text-bold text-right mt-1">Tipe Approval</label>
                                                                    <span class="col-form-label text-bold">:</span>
                                                                    <div class="col-6">
                                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="approvalType" value="{{ getApprovalTypeLabel($salesOrder->pendingApproval->type) }}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if(isWaitingApproval($salesOrder->status) && isApprovalTypeEdit($salesOrder->pendingApproval->type))
                                                        <div class="row">
                                                            <div class="col-12">
                                                            </div>
                                                            <div class="col edit-receipt-general-info-right">
                                                                <div class="form-group row customer-detail">
                                                                    <label for="revision" class="col-3 form-control-sm text-bold text-right mt-1">Revisi</label>
                                                                    <span class="col-form-label text-bold">:</span>
                                                                    <div class="col-6">
                                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="revision" value="{{ $mapRevisionBySalesOrderId[$salesOrder->id] ?? 0 }}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="tablePO">
                                                    <thead class="text-center text-bold text-dark">
                                                        <tr>
                                                            <td rowspan="2" class="align-middle table-head-number-transaction">No</td>
                                                            <td rowspan="2" class="align-middle table-head-code-transfer-transaction">SKU</td>
                                                            <td rowspan="2" class="align-middle table-head-name-transaction">Nama Produk</td>
                                                            <td rowspan="2" class="align-middle table-head-quantity-transaction">Qty</td>
                                                            <td colspan="{{ $totalWarehouses }}">Gudang</td>
                                                            <td rowspan="2" class="align-middle table-head-unit-transaction">Unit</td>
                                                            <td rowspan="2" class="align-middle table-head-price-transaction">Harga</td>
                                                            <td rowspan="2" class="align-middle table-head-total-transaction">Total</td>
                                                            <td colspan="2">Diskon</td>
                                                            <td rowspan="2" class="align-middle table-head-total-transaction">Netto</td>
                                                        </tr>
                                                        <tr>
                                                            @foreach($warehouses as $warehouse)
                                                                <td>{{ $warehouse->name }}</td>
                                                            @endforeach
                                                            <td class="align-middle table-head-discount-percentage-sales-order">%</td>
                                                            <td class="align-middle table-head-discount-amount-sales-order">Rupiah</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($salesOrder->salesOrderItems as $index => $salesOrderItem)
                                                            <tr class="text-dark">
                                                                <td class="text-center">{{ ++$index }}</td>
                                                                <td>{{ $salesOrderItem->product_sku }} </td>
                                                                <td>{{ $salesOrderItem->product_name }}</td>
                                                                <td class="text-right">{{ formatQuantity($salesOrderItem->quantity) }}</td>
                                                                @foreach($warehouses as $warehouse)
                                                                    <td class="text-right">{{ $productWarehouses[$salesOrder->id][$salesOrderItem->product_id][$warehouse->id] ?? '' }}</td>
                                                                @endforeach
                                                                <td>{{ $salesOrderItem->unit_name }}</td>
                                                                <td class="text-right">{{ formatPrice($salesOrderItem->price) }}</td>
                                                                <td class="text-right">{{ formatPrice($salesOrderItem->total) }}</td>
                                                                <td class="text-right">{{ $salesOrderItem->discount }}</td>
                                                                <td class="text-right">{{ formatPrice($salesOrderItem->discount_amount) }}</td>
                                                                <td class="text-right">{{ formatPrice($salesOrderItem->final_amount) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center text-bold h4 p-2"><i>Tidak Ada Data</i></td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                                <div class="form-group row justify-content-end subtotal-so">
                                                    <label for="total" class="col-2 col-form-label text-bold text-right text-dark">Total</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-2 mr-1">
                                                        <input type="text" id="total" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($salesOrder->subtotal) }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row justify-content-end total-so">
                                                    <label for="invoiceDiscount" class="col-2 col-form-label text-bold text-right text-dark">Diskon Faktur</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-2 mr-1">
                                                        <input type="text" id="invoiceDiscount" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($salesOrder->discount_amount) }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row justify-content-end total-so">
                                                    <label for="subtotal" class="col-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-2 mr-1">
                                                        <input type="text" id="subtotal" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($salesOrder->subtotal - $salesOrder->discount_amount) }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row justify-content-end total-so">
                                                    <label for="taxAmount" class="col-2 col-form-label text-bold text-right text-dark">PPN</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-2 mr-1">
                                                        <input type="text" id="taxAmount" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($salesOrder->tax_amount) }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row justify-content-end grandtotal-so">
                                                    <label for="grandTotal" class="col-2 col-form-label text-bold text-right text-dark">Grand Total</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-2 mr-1">
                                                        <input type="text" id="grandTotal" class="form-control-plaintext text-bold text-danger text-right text-lg" value="{{ formatPrice($salesOrder->grand_total) }}" readonly>
                                                    </div>
                                                </div>
                                                <hr>
                                                @if((!isWaitingApproval($salesOrder->status) || isApprovalTypeEdit($salesOrder->pendingApproval->type)) && !isCancelled($salesOrder->status))
                                                    <div class="form-row justify-content-center">
                                                        <div class="col-2">
                                                            <button type="button" class="btn btn-danger btn-block text-bold cancel-order" id="btnCancel-{{ $key }}" data-toggle="modal" data-target="#modalCancelOrder" data-id="{{ $salesOrder->id }}" data-number="{{ $salesOrder->number }}" tabindex="6">Batalkan</button>
                                                        </div>
                                                        <div class="col-2">
                                                            <button type="submit" class="btn btn-info btn-block text-bold edit-order" formaction="{{ route('sales-orders.edit', $salesOrder->id) }}" formmethod="GET" id="btnEdit-{{ $key }}" data-index="{{ $key }}">Ubah</button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="container so-update-container text-dark mt-2">
                                                <h3 class="text-center text-bold text-dark">Tidak Ada Data</h3>
                                            </div>
                                        @endforelse
                                    </div>
                                    @if(($salesOrders->count() > 0) && ($salesOrders->count() != 1))
                                        <a class="carousel-control-prev" href="#so-carousel" role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="carousel-control-next " href="#so-carousel" role="button" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                    @endif
                                </div>
                            </form>

                            <div class="modal" id="modalCancelOrder" tabindex="-1" role="dialog" aria-labelledby="modalCancelOrder" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" class="h2 text-bold">&times;</span>
                                            </button>
                                            <h4 class="modal-title">Batalkan Sales Order - <span id="modalOrderNumber"></span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST" id="deleteForm">
                                                @csrf
                                                @method('DELETE')
                                                <div class="form-group row">
                                                    <label for="status" class="col-2 col-form-label text-bold">Status</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-3">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="status" id="status" value="CANCEL" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group subtotal-so">
                                                    <label for="description" class="col-form-label">Deskripsi</label>
                                                    <input type="text" class="form-control" name="description" id="description">
                                                </div>
                                                <hr>
                                                <div class="form-row justify-content-center">
                                                    <div class="col-3">
                                                        <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit">Simpan</button>
                                                    </div>
                                                    <div class="col-3">
                                                        <button type="button" class="btn btn-outline-secondary btn-block text-bold" data-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            const form = $('#form');
            const modalCancelOrder = $('#modalCancelOrder');

            form.on('click', '.edit-order', function (e) {
                if(!$(this).attr('data-validated')) {
                    e.preventDefault();

                    let subjectIndex = $(this).data('index');
                    $('#subjectIndex').val(subjectIndex);
                    $('#modalPasswordEdit').modal('show');
                } else {
                    $(this).removeAttr('data-validated');
                }
            });

            form.on('click', '.cancel-order', function () {
                const orderId = $(this).data('id');
                const orderNumber = $(this).data('number');
                const url = `{{ route('sales-orders.destroy', '') }}` + '/' + orderId;

                $('#modalOrderNumber').text(orderNumber);
                $('#deleteForm').attr('action', url);
            });

            modalCancelOrder.on('show.bs.modal', function (e) {
                $('#description').attr('required', true);
            })

            modalCancelOrder.on('hide.bs.modal', function (e) {
                $('#description').removeAttr('required');
            })

            $('#btnSubmit').on('click', function(event) {
                event.preventDefault();

                let checkForm = document.getElementById('deleteForm').checkValidity();
                if(!checkForm) {
                    document.getElementById('deleteForm').reportValidity();
                    return false;
                }

                $('#deleteForm').submit();
            });
        });

        function submitForm(index) {
            const sourceButton = $('#btnEdit-' + index);
            sourceButton.attr('data-validated', 'true');
            sourceButton.trigger('click');
        }
    </script>
@endpush
