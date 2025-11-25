@extends('layouts.admin')

@push('addon-style')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Delivery Order</h1>
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
                            <div class="container so-update-container ">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <label for="number" class="col-5 text-right mt-2">Number</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="number" value="{{ $deliveryOrder->number }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <label for="invoiceNumber" class="col-4 text-right text-bold mt-2">Invoice Number</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="invoiceNumber" value="{{ $deliveryOrder->salesOrder->number }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="date" class="col-5 text-right text-bold mt-2">Date</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="date" value="{{ formatDate($deliveryOrder->date, 'd-m-Y') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="branch" class="col-4 text-right text-bold mt-2">Branch</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="branch" value="{{ $deliveryOrder->branch->name }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="status" class="col-5 text-right text-bold mt-2">Status</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="status" value="{{ getDeliveryOrderStatusLabel($deliveryOrder->status) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="customer" class="col-4 text-right text-bold mt-2">Customer</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="customer" value="{{ $deliveryOrder->customer->name }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="user" class="col-5 text-right text-bold mt-2">Admin</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="user" value="{{ $deliveryOrder->user->username }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="address" class="col-4 text-right text-bold mt-2">Address</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="address" value="{{ $deliveryOrder->customer->address }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(isUpdated($deliveryOrder->status) || (isWaitingApproval($deliveryOrder->status) && isApprovalTypeEdit($deliveryOrder->pendingApproval->type)))
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group row detail-po-information-row">
                                                <label for="revision" class="col-5 text-right text-bold mt-2">Revision</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6">
                                                    <input type="text" class="form-control-plaintext text-bold text-dark" id="revision" value="{{ $deliveryOrder->revision ?? 0 }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                <thead class="text-center text-bold text-dark">
                                    <tr>
                                        <td class="align-middle table-head-number-transaction">No</td>
                                        <td class="align-middle table-head-code-transfer-transaction">SKU</td>
                                        <td class="align-middle table-head-name-transaction">Product Name</td>
                                        <td class="align-middle table-head-quantity-transaction">Qty</td>
                                        <td class="align-middle table-head-unit-transaction">Unit</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deliveryOrderItems as $key => $deliveryOrderItem)
                                        <tr class="text-dark">
                                            <td class="text-center">{{ ++$key }}</td>
                                            <td>{{ $deliveryOrderItem->product->sku }} </td>
                                            <td>{{ $deliveryOrderItem->product->name }}</td>
                                            <td class="text-right">{{ formatQuantity($deliveryOrderItem->quantity) }}</td>
                                            <td class="text-center">{{ $deliveryOrderItem->unit->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <hr>
                            <div class="form-row justify-content-center">
                                <div class="col-2">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Back to List</a>
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
@endpush
