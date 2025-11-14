@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Purchase Return</h1>
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
                            <form action="{{ route('purchase-returns.update', $purchaseReturn->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="number" class="col-2 col-form-label text-bold text-right">Return Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control form-control-sm text-bold" name="number" id="number" value="{{ $purchaseReturn->number }}" readonly>
                                                </div>
                                                <label for="date" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2 mt-1">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold" name="date" id="date" value="{{ formatDate($purchaseReturn->date, 'd-M-y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row delivery-order-customer-input">
                                        <label for="goodsReceipt" class="col-2 col-form-label text-bold text-right">Receipt Number</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="goods_receipt" id="goodsReceipt" value="{{ $purchaseReturn->goodsReceipt->number }}" readonly>
                                        </div>
                                        <label for="receivedDate" class="col-2 col-form-label text-bold text-right sales-order-middle-input">Received Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="received_date" id="receivedDate" value="{{ $purchaseReturn->received_date ? formatDate($purchaseReturn->received_date, 'd-M-y') : '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="branch" class="col-2 col-form-label text-bold text-right">Branch</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="branch" id="branch" value="{{ $purchaseReturn->goodsReceipt->branch->name }}" readonly>
                                        </div>
                                        <label for="receiptStatus" class="col-2 col-form-label text-bold text-right sales-order-middle-last-input">Receipt Status</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="receipt_status" id="receiptStatus" value="{{ getPurchaseReturnReceiptStatusLabel($purchaseReturn->receipt_status) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row subtotal-so">
                                        <label for="supplier" class="col-2 col-form-label text-bold text-right">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-3 mt-1">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold" name="supplier" id="supplier" value="{{ $purchaseReturn->supplier->name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div>
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="align-middle table-head-number-delivery-order">No</td>
                                                <td class="align-middle table-head-code-delivery-order">SKU</td>
                                                <td class="align-middle">Product Name</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Order Qty</td>
                                                <td class="align-middle table-head-unit-delivery-order">Unit</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Return Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Received Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Cut Bill Qty</td>
                                                <td class="align-middle table-head-quantity-delivery-order">Remaining Qty</td>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTable">
                                            @foreach($purchaseReturn->purchaseReturnItems as $index => $purchaseReturnItem)
                                                <tr class="text-bold text-dark" id="{{ $index }}">
                                                    <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_sku[]" id="productSku-{{ $index }}" value="{{ $purchaseReturnItem->product->sku }}" title="" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark readonly-input" name="product_name[]" id="productName-{{ $index }}" value="{{ $purchaseReturnItem->product->name }}" title="" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="order_quantity[]" id="orderQuantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->receipt_quantity) }}" title="" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-center readonly-input" name="unit[]" id="unit-{{ $index }}" value="{{ $purchaseReturnItem->unit->name }}" title="" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="quantity[]" id="quantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->quantity) }}" title="" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="received_quantity[]" id="receivedQuantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->received_quantity) }}" title="" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="cut_bill_quantity[]" id="cutBillQuantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->cut_bill_quantity) }}" title="" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm text-bold text-dark text-right readonly-input" name="remaining_quantity[]" id="remainingQuantity-{{ $index }}" value="{{ formatQuantity($purchaseReturnItem->remaining_quantity) }}" title="" readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="form-row justify-content-center">
                                        <div class="col-2">
                                            <a href="{{ route('purchase-returns.index') }}" class="btn btn-outline-primary btn-block text-bold">Back to List</a>
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
    </script>
@endpush
