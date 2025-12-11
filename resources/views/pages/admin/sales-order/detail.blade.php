@extends('layouts.admin')

@push('addon-style')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Sales Order</h1>
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
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="number" value="{{ $salesOrder->number }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <label for="branch" class="col-3 text-right text-bold mt-2">Cabang</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="branch" value="{{ $salesOrder->branch->name }}" readonly>
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
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="date" value="{{ formatDate($salesOrder->date, 'd-m-Y') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="customer" class="col-3 text-right text-bold mt-2">Customer</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="customer" value="{{ $salesOrder->customer->name }}" readonly>
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
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="status" value="{{ getSalesOrderStatusLabel($salesOrder->status) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="marketing" class="col-3 text-right text-bold mt-2">Sales</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="marketing" value="{{ $salesOrder->marketing->name }}" readonly>
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
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="user" value="{{ $salesOrder->user->username }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="dueDate" class="col-3 text-right text-bold mt-2">Jatuh Tempo</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="dueDate" value="{{ getDueDate($salesOrder->date, $salesOrder->tempo, 'd-m-Y') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="user" class="col-5 text-right text-bold mt-2">PKP</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="isTaxable" value="{{ $salesOrder->is_taxable ? 'Ya' : 'Tidak' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row detail-po-information-row">
                                            <label for="note" class="col-3 text-right text-bold mt-2">Note</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-7">
                                                <input type="text" class="form-control-plaintext text-bold text-dark" id="note" value="{{ $salesOrder->note }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(isUpdated($salesOrder->status) || (isWaitingApproval($salesOrder->status) && isApprovalTypeEdit($salesOrder->pendingApproval->type)))
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group row detail-po-information-row">
                                                <label for="revision" class="col-5 text-right text-bold mt-2">Revisi</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6">
                                                    <input type="text" class="form-control-plaintext text-bold text-dark" id="revision" value="{{ $salesOrder->revision ?? 0 }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                <thead class="text-center text-bold text-dark">
                                    <tr>
                                        <td rowspan="2" class="align-middle table-head-number-transaction">No</td>
                                        <td rowspan="2" class="align-middle table-head-code-transfer-transaction">SKU</td>
                                        <td rowspan="2" class="align-middle table-head-name-transaction">Nama Produk</td>
                                        <td rowspan="2" class="align-middle table-head-quantity-transaction">Qty</td>
                                        <td rowspan="2" class="align-middle table-head-unit-transaction">Unit</td>
                                        <td rowspan="2" class="align-middle table-head-price-transaction">Harga</td>
                                        <td rowspan="2" class="align-middle table-head-total-transaction">Total</td>
                                        <td colspan="2">Diskon</td>
                                        <td rowspan="2" class="align-middle table-head-total-transaction">Netto</td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle table-head-discount-percentage-sales-order">%</td>
                                        <td class="align-middle table-head-discount-amount-sales-order">Rupiah</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesOrderItems as $key => $salesOrderItem)
                                        <tr class="text-dark">
                                            <td class="text-center">{{ ++$key }}</td>
                                            <td>{{ $salesOrderItem->product_sku }} </td>
                                            <td>{{ $salesOrderItem->product_name }}</td>
                                            <td class="text-right">{{ formatQuantity($salesOrderItem->quantity) }}</td>
                                            <td>{{ $salesOrderItem->unit_name }}</td>
                                            <td class="text-right">{{ formatPrice($salesOrderItem->price) }}</td>
                                            <td class="text-right">{{ formatPrice($salesOrderItem->total) }}</td>
                                            <td class="text-right">{{ $salesOrderItem->discount }}</td>
                                            <td class="text-right">{{ formatPrice($salesOrderItem->discount_amount) }}</td>
                                            <td class="text-right">{{ formatPrice($salesOrderItem->final_amount) }}</td>
                                        </tr>
                                    @endforeach
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
