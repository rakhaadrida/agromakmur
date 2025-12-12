@extends('layouts.admin')

@push('addon-style')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Barang Masuk</h1>
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
                                            <label for="number" class="col-5 text-right mt-2">Nomor</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="number" value="{{ $goodsReceipt->number }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <label for="branch" class="col-3 text-right text-bold mt-2">Cabang</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="branch" value="{{ $goodsReceipt->branch->name }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="date" class="col-5 text-right text-bold mt-2">Tanggal</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="date" value="{{ formatDate($goodsReceipt->date, 'd-m-Y') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="supplier" class="col-3 text-right text-bold mt-2">Supplier</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="supplier" value="{{ $goodsReceipt->supplier->name }}" readonly>
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
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="status" value="{{ getGoodsReceiptStatusLabel($goodsReceipt->status) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="warehouse" class="col-3 text-right text-bold mt-2">Gudang</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="warehouse" value="{{ $goodsReceipt->warehouse->name }}" readonly>
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
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="user" value="{{ $goodsReceipt->user->username }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="dueDate" class="col-3 text-right text-bold mt-2">Jatuh Tempo</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="dueDate" value="{{ getDueDate($goodsReceipt->date, $goodsReceipt->tempo, 'd-m-Y') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(isUpdated($goodsReceipt->status) || (isWaitingApproval($goodsReceipt->status) && isApprovalTypeEdit($goodsReceipt->pendingApproval->type)))
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group row detail-po-information-row">
                                                <label for="revision" class="col-5 text-right text-bold mt-2">Revisi</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6">
                                                    <input type="text" class="form-control-plaintext text-bold text-dark" id="revision" value="{{ $goodsReceipt->revision ?? 0 }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                <thead class="text-center text-bold text-dark">
                                    <tr>
                                        <td>No</td>
                                        <td class="table-head-shipping-cost-transaction">SKU</td>
                                        <td>Nama Produk</td>
                                        <td class="table-head-quantity-transaction">Qty</td>
                                        <td class="table-head-unit-transaction">Unit</td>
                                        <td class="table-head-price-transaction">Harga</td>
                                        <td class="table-head-price-transaction">Upah</td>
                                        <td class="table-head-price-transaction">Ongkos Kirim</td>
                                        <td class="table-head-price-transaction">Harga Modal</td>
                                        <td class="table-head-price-transaction">Total</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($goodsReceiptItems as $key => $goodsReceiptItem)
                                        <tr class="text-dark">
                                            <td class="text-center">{{ ++$key }}</td>
                                            <td>{{ $goodsReceiptItem->product->sku }} </td>
                                            <td>{{ $goodsReceiptItem->product->name }}</td>
                                            <td class="text-right">{{ formatQuantity($goodsReceiptItem->quantity) }}</td>
                                            <td class="text-center">{{ $goodsReceiptItem->unit->name }}</td>
                                            <td class="text-right">{{ formatPrice($goodsReceiptItem->price) }}</td>
                                            <td class="text-right">{{ formatPrice($goodsReceiptItem->wages) }}</td>
                                            <td class="text-right">{{ formatPrice($goodsReceiptItem->shipping_cost) }}</td>
                                            <td class="text-right">{{ formatPrice($goodsReceiptItem->cost_price) }}</td>
                                            <td class="text-right">{{ formatPrice($goodsReceiptItem->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="form-group row justify-content-end subtotal-so">
                                <label for="subtotal" class="col-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-2 mr-1">
                                    <input type="text" id="subtotal" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($goodsReceipt->subtotal) }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row justify-content-end total-so">
                                <label for="taxAmount" class="col-2 col-form-label text-bold text-right text-dark">PPN</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-2 mr-1">
                                    <input type="text" id="taxAmount" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($goodsReceipt->tax_amount) }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row justify-content-end grandtotal-so">
                                <label for="grandTotal" class="col-2 col-form-label text-bold text-right text-dark">Grand Total</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-2 mr-1">
                                    <input type="text" id="grandTotal" class="form-control-plaintext text-bold text-danger text-right text-lg" value="{{ formatPrice($goodsReceipt->grand_total) }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row justify-content-end mt-1">
                                <label for="paymentAmount" class="col-2 col-form-label text-bold text-right text-dark">Pembayaran</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-2 mr-1">
                                    <input type="text" id="paymentAmount" class="form-control-plaintext text-bold text-secondary text-right text-lg" value="{{ formatPrice($goodsReceipt->payment_amount) }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row justify-content-end grandtotal-so">
                                <label for="outstandingAmount" class="col-2 col-form-label text-bold text-right text-dark">Sisa Bayar</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-2 mr-1">
                                    <input type="text" id="outstandingAmount" class="form-control-plaintext text-bold text-danger text-right text-lg" value="{{ formatPrice($goodsReceipt->outstanding_amount) }}" readonly>
                                </div>
                            </div>
                            <hr>
                            <div class="form-row justify-content-center">
                                <div class="col-2">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Kembali ke Daftar</a>
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
