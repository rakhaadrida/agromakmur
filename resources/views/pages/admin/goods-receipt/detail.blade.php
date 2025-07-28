@extends('layouts.admin')

@push('addon-style')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Goods Receipt</h1>
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
                                                <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="number" value="{{ $goodsReceipt->number }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <label for="supplier" class="col-3 text-right text-bold mt-2">Supplier</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="supplier" value="{{ $goodsReceipt->supplier->name }}" >
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
                                                <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="date" value="{{ formatDate($goodsReceipt->date, 'd-m-Y') }}" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="warehouse" class="col-3 text-right text-bold mt-2">Warehouse</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="warehouse" value="{{ $goodsReceipt->warehouse->name }}">
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
                                                <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="status" value="{{ getgoodsReceiptstatusLabel($goodsReceipt->status) }}" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="dueDate" class="col-3 text-right text-bold mt-2">Due Date</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="dueDate" value="{{ getDueDate($goodsReceipt->date, $goodsReceipt->tempo, 'd-m-Y') }}" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                <thead class="text-center text-bold text-dark">
                                    <tr>
                                        <td>No</td>
                                        <td>SKU</td>
                                        <td>Product Name</td>
                                        <td>Qty</td>
                                        <td>Price</td>
                                        <td>Total</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($goodsReceiptItems as $key => $goodsReceiptItem)
                                        <tr class="text-dark">
                                          <td class="text-center">{{ ++$key }}</td>
                                          <td>{{ $goodsReceiptItem->product->sku }} </td>
                                          <td>{{ $goodsReceiptItem->product->name }}</td>
                                          <td class="text-right">{{ formatQuantity($goodsReceiptItem->quantity) }}</td>
                                          <td class="text-right">{{ formatCurrency($goodsReceiptItem->price) }}</td>
                                          <td class="text-right">{{ formatCurrency($goodsReceiptItem->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="form-group row justify-content-end subtotal-so">
                                <label for="subtotal" class="col-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-2 mr-1">
                                    <input type="text" id="subtotal" readonly class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatCurrency($goodsReceipt->subtotal) }}" />
                                </div>
                            </div>
                            <div class="form-group row justify-content-end total-so">
                                <label for="taxAmount" class="col-2 col-form-label text-bold text-right text-dark">Tax Amount</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-2 mr-1">
                                    <input type="text" id="taxAmount" readonly class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatCurrency($goodsReceipt->tax_amount) }}" />
                                </div>
                            </div>
                            <div class="form-group row justify-content-end grandtotal-so">
                                <label for="grandTotal" class="col-2 col-form-label text-bold text-right text-dark">Grand Total</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-2 mr-1">
                                    <input type="text" id="grandTotal" readonly class="form-control-plaintext text-bold text-danger text-right text-lg" value="{{ formatCurrency($goodsReceipt->grand_total) }}" />
                                </div>
                            </div>
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
