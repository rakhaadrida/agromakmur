@extends('layouts.admin')

@push('addon-style')
  <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Edit Plan Order</h1>
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
                            <form action="{{ route('plan-orders.index-edit') }}" method="GET" id="form">
                                <div class="container so-container">
                                    <div class="form-group row">
                                        <label for="number" class="col-2 col-form-label text-bold text-right">PO Number</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control form-control-sm text-bold mt-1" name="number" id="number" value="{{ $number }}" tabindex="1" autofocus>
                                        </div>
                                        <label for="supplier" class="col-auto col-form-label text-bold text-right filter-supplier-receipt">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4">
                                            <select class="selectpicker supplier-params-select-picker" name="supplier_id" id="supplier" data-live-search="true" data-size="6" title="Enter or Choose Supplier Name" tabindex="2">
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}" data-tokens="{{ $supplier->name }}" @if($supplierId == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row filter-date-receipt">
                                        <label for="startDate" class="col-2 col-form-label text-bold text-right">Start Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" tabindex="3">
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-bold text-right filter-final-date-receipt">Final Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}" tabindex="4">
                                        </div>
                                        <div class="col-1 mt-1 btn-search-receipt">
                                            <button type="submit" id="btnSearch" class="btn btn-primary btn-sm btn-block text-bold" tabindex="5">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="so-carousel" class="carousel slide" data-interval="false" wrap="false">
                                    <div class="carousel-inner">
                                        @forelse($planOrders as $key => $planOrder)
                                            <div class="carousel-item @if(!$key) active @endif">
                                                <div class="container so-update-container text-dark">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <label for="receiptNumber" class="col-2 form-control-sm text-bold text-right mt-1">PO Number</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-2">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="orderNumber" value="{{ $planOrder->number }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col edit-receipt-general-info-right">
                                                            <div class="form-group row">
                                                                <label for="branch" class="col-3 form-control-sm text-bold text-right mt-1">Branch</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-4">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="branch" value="{{ $planOrder->branch_name }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group row customer-detail">
                                                                <label for="date" class="col-2 form-control-sm text-bold text-right mt-1">PO Date</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-2">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="date" value="{{ formatDate($planOrder->date, 'd-m-Y') }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col edit-receipt-general-info-right">
                                                            <div class="form-group row customer-detail">
                                                                <label for="supplier" class="col-3 form-control-sm text-bold text-right mt-1">Supplier</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-4">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="supplier" value="{{ $planOrder->supplier_name }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group row customer-detail">
                                                                <label for="status" class="col-2 form-control-sm text-bold text-right mt-1">Status</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-6">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="status" value="{{ getPlanOrderStatusLabel($planOrder->status) }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col edit-receipt-general-info-right">
                                                            <div class="form-group row customer-detail">
                                                                <label for="user" class="col-3 form-control-sm text-bold text-right mt-1">Admin</label>
                                                                <span class="col-form-label text-bold">:</span>
                                                                <div class="col-3">
                                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="user" value="{{ $planOrder->user_name }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="tablePO">
                                                    <thead class="text-center text-bold text-dark">
                                                        <tr>
                                                            <td class="table-head-delete-transaction">No</td>
                                                            <td class="table-head-code-transaction">SKU</td>
                                                            <td class="table-head-name-transaction">Product Name</td>
                                                            <td class="table-head-quantity-transaction">Qty</td>
                                                            <td class="table-head-quantity-transaction">Unit</td>
                                                            <td class="table-head-price-transaction">Price</td>
                                                            <td class="table-head-total-transaction">Total</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($planOrder->planOrderItems as $index => $planOrderItem)
                                                            <tr class="text-dark">
                                                                <td class="text-center">{{ ++$index }}</td>
                                                                <td>{{ $planOrderItem->product->sku }} </td>
                                                                <td>{{ $planOrderItem->product->name }}</td>
                                                                <td class="text-right">{{ formatQuantity($planOrderItem->quantity) }}</td>
                                                                <td>{{ $planOrderItem->unit->name }}</td>
                                                                <td class="text-right">{{ formatPrice($planOrderItem->price) }}</td>
                                                                <td class="text-right">{{ formatPrice($planOrderItem->total) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center text-bold h4 p-2"><i>No Data Available</i></td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                                <div class="form-group row justify-content-end subtotal-so">
                                                    <label for="subtotal" class="col-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-2 mr-1">
                                                        <input type="text" id="subtotal" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($planOrder->subtotal) }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row justify-content-end total-so">
                                                    <label for="taxAmount" class="col-2 col-form-label text-bold text-right text-dark">Tax Amount</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-2 mr-1">
                                                        <input type="text" id="taxAmount" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($planOrder->tax_amount) }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row justify-content-end grandtotal-so">
                                                    <label for="grandTotal" class="col-2 col-form-label text-bold text-right text-dark">Grand Total</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-2 mr-1">
                                                        <input type="text" id="grandTotal" class="form-control-plaintext text-bold text-danger text-right text-lg" value="{{ formatPrice($planOrder->grand_total) }}" readonly>
                                                    </div>
                                                </div>
                                                <hr>
                                                @if(!isCancelled($planOrder->status))
                                                    <div class="form-row justify-content-center">
                                                        <div class="col-2">
                                                            <button type="button" class="btn btn-danger btn-block text-bold cancel-order" id="btnCancel-{{ $key }}" data-toggle="modal" data-target="#modalCancelOrder" data-id="{{ $planOrder->id }}" data-number="{{ $planOrder->number }}" tabindex="6">Cancel Order</button>
                                                        </div>
                                                        <div class="col-2">
                                                            <button type="submit" class="btn btn-info btn-block text-bold edit-order" formaction="{{ route('plan-orders.edit', $planOrder->id) }}" formmethod="GET" id="btnEdit-{{ $key }}" data-index="{{ $key }}">Edit</button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="container so-update-container text-dark mt-2">
                                                <h3 class="text-center text-bold text-dark">No Data Available</h3>
                                            </div>
                                        @endforelse
                                    </div>
                                    @if(($planOrders->count() > 0) && ($planOrders->count() != 1))
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
                                            <h4 class="modal-title">Cancel Plan Order - <span id="modalOrderNumber"></span></h4>
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
                                                    <label for="description" class="col-form-label">Description</label>
                                                    <input type="text" class="form-control" name="description" id="description">
                                                    <input type="hidden" class="form-control" name="number" value="{{ $number }}">
                                                    <input type="hidden" class="form-control" name="supplier_id" value="{{ $supplierId }}">
                                                    <input type="hidden" class="form-control" name="start_date" value="{{ $startDate }}">
                                                    <input type="hidden" class="form-control" name="final_date" value="{{ $finalDate }}">
                                                </div>
                                                <hr>
                                                <div class="form-row justify-content-center">
                                                    <div class="col-3">
                                                        <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit">Submit</button>
                                                    </div>
                                                    <div class="col-3">
                                                        <button type="button" class="btn btn-outline-secondary btn-block text-bold" data-dismiss="modal">Close</button>
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
                const url = `{{ route('plan-orders.destroy', '') }}` + '/' + orderId;

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
