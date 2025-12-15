@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Histori Approval - {{ getApprovalSubjectTypeLabel($approval->subject_label) }}</h1>
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
                            <div class="container so-update-container text-dark">
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group row transaction-number">
                                            <label for="number" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Nomor Transaksi</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-4 col-md-3">
                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="number" id="number" value="{{ $approval->subject->number }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    @if(isApprovalSubjectTypeSalesOrder($approval->subject_label) || isApprovalSubjectTypeGoodsReceipt($approval->subject_label) || isApprovalSubjectTypeDeliveryOrder($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row">
                                                <label for="branch" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Cabang</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="branch" id="branch" value="{{ $approval->subject->branch->name }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(isApprovalSubjectTypeSalesReturn($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row">
                                                <label for="branch" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Cabang</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="branch" id="branch" value="{{ $approval->subject->salesOrder->branch->name }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(isApprovalSubjectTypePurchaseReturn($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row">
                                                <label for="branch" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Cabang</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="branch" id="branch" value="{{ $approval->subject->goodsReceipt->branch->name }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row">
                                                <label for="clientLabel" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">{{ $approval->client_label }}</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="client_label" id="clientLabel" value="{{ $approval->client_name }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row" style="margin-top: -5px">
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group row transaction-date approval-detail-row">
                                            <label for="date" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Tanggal Transaksi</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-4">
                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="date" id="date" value="{{ formatDate($approval->subject->date, 'd-M-y') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group row approval-detail-row">
                                            <label for="clientLabel" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">{{ $approval->client_label }}</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6 col-sm-5 col-md-7">
                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="client_label" id="clientLabel" value="{{ $approval->client_name }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: -5px">
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group row transaction-date approval-detail-row">
                                            <label for="status" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Status</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-6 col-md-7">
                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="status" id="status" value="{{ getApprovalStatusLabel($approval->status) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    @if(isApprovalSubjectTypeSalesOrder($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row">
                                                <label for="marketing" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Sales</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="marketing" id="marketing" value="{{ $approval->subject->marketing->name }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(isApprovalSubjectTypeGoodsReceipt($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row">
                                                <label for="warehouse" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Gudang</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="warehouse" id="warehouse" value="{{ $approval->subject->warehouse->name }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(isApprovalSubjectTypeDeliveryOrder($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row">
                                                <label for="type" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-dark text-right mt-1">Tipe Approval</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-md text-bold text-dark" name="type" id="type" value="{{ getApprovalTypeLabel($approval->type) }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(isApprovalSubjectTypeSalesReturn($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row">
                                                <label for="type" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-dark text-right mt-1">Nomor SO</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-md text-bold text-dark" name="type" id="type" value="{{ $approval->subject->salesOrder->number }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(isApprovalSubjectTypePurchaseReturn($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row">
                                                <label for="type" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-dark text-right mt-1">Nomor BM</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-md text-bold text-dark" name="type" id="type" value="{{ $approval->subject->goodsReceipt->number }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        @if(isApprovalSubjectTypeSalesOrder($approval->subject_label) || isApprovalSubjectTypeGoodsReceipt($approval->subject_label))
                                            <div class="form-group row approval-detail-row approval-sales-order-description">
                                                <label for="type" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-dark text-right mt-1">Tipe Approval</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-md text-bold text-dark" name="type" id="type" value="{{ getApprovalTypeLabel($approval->type) }}" readonly>
                                                </div>
                                            </div>
                                        @elseif(isApprovalSubjectTypeDeliveryOrder($approval->subject_label) && isApprovalTypeCancel($approval->type))
                                            <div class="form-group row approval-detail-row approval-sales-order-description">
                                                <label for="description" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Deskripsi</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="description" id="description" value="{{ $approval->description }}" readonly>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if(isApprovalSubjectTypeSalesOrder($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row-lg">
                                                <label for="dueDate" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Jatuh Tempo</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="due_date" id="dueDate" value="{{ getDueDate($approval->subject->date, $approval->subject->tempo, 'd-M-y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(isApprovalSubjectTypeGoodsReceipt($approval->subject_label) && isApprovalTypeCancel($approval->type))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row-lg">
                                                <label for="description" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Deksripsi</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="description" id="description" value="{{ $approval->description }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row" style="margin-top: 5px;">
                                    <div class="col-12 col-lg-6">
                                        @if(isApprovalSubjectTypeSalesOrder($approval->subject_label))
                                            @if(isApprovalTypeApprovalLimit($approval->type))
                                                <div class="form-group row approval-detail-row">
                                                    <label for="limit" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-dark text-right mt-1" style="font-size: 16px">Limit</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-md bg-warning text-danger text-bold text-lg" name="limit" id="limit" value="{{ formatPrice($approval->subject->customer->credit_limit) }}" readonly>
                                                    </div>
                                                </div>
                                            @elseif(isApprovalTypeCancel($approval->type))
                                                <div class="form-group row approval-sales-order-description-cancel">
                                                    <label for="description" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-dark text-right mt-1">Deskripsi</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-dark text-bold" name="description" id="description" value="{{ $approval->description }}" readonly>
                                                    </div>
                                                </div>
                                            @endif
                                        @elseif(isApprovalSubjectTypeSalesReturn($approval->subject_label) || isApprovalSubjectTypePurchaseReturn($approval->subject_label))
                                            <div class="form-group row approval-sales-order-description-cancel">
                                                <label for="type" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-dark text-right mt-1">Tipe Approval</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-md text-bold text-dark" name="type" id="type" value="{{ getApprovalTypeLabel($approval->type) }}" readonly>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if(isApprovalSubjectTypeSalesReturn($approval->subject_label) || isApprovalSubjectTypePurchaseReturn($approval->subject_label))
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row approval-sales-return-type">
                                                <label for="description" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Deskripsi</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="description" id="description" value="{{ $approval->description }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="tablePO">
                                @if(isApprovalSubjectTypeDeliveryOrder($approval->subject_label))
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td class="align-middle table-head-number-transaction">No</td>
                                            <td class="align-middle table-head-code-transfer-transaction">SKU</td>
                                            <td class="align-middle table-head-name-transaction">Nama Produk</td>
                                            <td class="align-middle table-head-quantity-transaction">Qty</td>
                                            <td class="align-middle table-head-unit-transaction">Unit</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approval->approvalItems as $key => $approvalItem)
                                            <tr class="text-dark">
                                                <td class="text-center">{{ ++$key }}</td>
                                                <td>{{ $approvalItem->product->sku }} </td>
                                                <td>{{ $approvalItem->product->name }}</td>
                                                <td class="text-right">{{ formatQuantity($approvalItem->quantity) }}</td>
                                                <td class="text-center">{{ $approvalItem->unit->name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @elseif(isApprovalSubjectTypeGoodsReceipt($approval->subject_label))
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td class="table-head-number-transaction">No</td>
                                            <td class="table-head-shipping-cost-transaction">SKU</td>
                                            <td>Nama Produk</td>
                                            <td>Qty</td>
                                            <td class="table-head-unit-transaction">Unit</td>
                                            <td class="table-head-price-transaction">Harga</td>
                                            <td class="table-head-price-transaction">Upah</td>
                                            <td class="table-head-price-transaction">Ongkos Kirim</td>
                                            <td class="table-head-price-transaction">Harga Modal</td>
                                            <td class="table-head-shipping-cost-transaction">Total</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approval->approvalItems as $key => $approvalItem)
                                            <tr class="text-dark">
                                                <td class="text-center">{{ ++$key }}</td>
                                                <td>{{ $approvalItem->product->sku }} </td>
                                                <td>{{ $approvalItem->product->name }}</td>
                                                <td class="text-right">{{ formatQuantity($approvalItem->quantity) }}</td>
                                                <td class="text-center">{{ $approvalItem->unit->name }}</td>
                                                <td class="text-right">{{ formatPrice($approvalItem->price) }}</td>
                                                <td class="text-right">{{ formatPrice($approvalItem->wages) }}</td>
                                                <td class="text-right">{{ formatPrice($approvalItem->shipping_cost) }}</td>
                                                <td class="text-right">{{ formatPrice($approvalItem->cost_price) }}</td>
                                                <td class="text-right">{{ formatPrice($approvalItem->total) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @elseif(isApprovalSubjectTypeSalesReturn($approval->subject_label))
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td class="align-middle table-head-number-transaction">No</td>
                                            <td class="align-middle table-head-code-transfer-transaction">SKU</td>
                                            <td class="align-middle table-head-name-transaction">Nama Produk</td>
                                            <td class="align-middle table-head-return-quantity-transaction">Qty Order</td>
                                            <td class="align-middle table-head-unit-transaction">Unit</td>
                                            <td class="align-middle table-head-return-quantity-transaction">Qty Retur</td>
                                            <td class="align-middle table-head-return-quantity-transaction">Qty Dikirim</td>
                                            <td class="align-middle table-head-return-quantity-transaction">Potong Tagihan</td>
                                            <td class="align-middle table-head-return-quantity-transaction">Sisa Qty</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approval->approvalItems as $key => $approvalItem)
                                            <tr class="text-dark">
                                                <td class="text-center">{{ ++$key }}</td>
                                                <td>{{ $approvalItem->product->sku }} </td>
                                                <td>{{ $approvalItem->product->name }}</td>
                                                <td class="text-right">{{ formatQuantity($approvalItem->order_quantity) }}</td>
                                                <td class="text-center">{{ $approvalItem->unit->name }}</td>
                                                <td class="text-right">{{ formatQuantity($approvalItem->quantity) }}</td>
                                                <td class="text-right">{{ formatQuantity($approvalItem->delivered_quantity) }}</td>
                                                <td class="text-right">{{ formatQuantity($approvalItem->cut_bill_quantity) }}</td>
                                                <td class="text-right">{{ formatQuantity($approvalItem->remaining_quantity) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @elseif(isApprovalSubjectTypePurchaseReturn($approval->subject_label))
                                    <thead class="text-center text-bold text-dark">
                                    <tr>
                                        <td class="align-middle table-head-number-transaction">No</td>
                                        <td class="align-middle table-head-code-transfer-transaction">SKU</td>
                                        <td class="align-middle table-head-name-transaction">Nama Produk</td>
                                        <td class="align-middle table-head-return-quantity-transaction">Qty Masuk</td>
                                        <td class="align-middle table-head-unit-transaction">Unit</td>
                                        <td class="align-middle table-head-return-quantity-transaction">Qty Retur</td>
                                        <td class="align-middle table-head-return-quantity-transaction">Qty Terima</td>
                                        <td class="align-middle table-head-return-quantity-transaction">Potong Tagihan</td>
                                        <td class="align-middle table-head-return-quantity-transaction">Sisa Qty</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($approval->approvalItems as $key => $approvalItem)
                                        <tr class="text-dark">
                                            <td class="text-center">{{ ++$key }}</td>
                                            <td>{{ $approvalItem->product->sku }} </td>
                                            <td>{{ $approvalItem->product->name }}</td>
                                            <td class="text-right">{{ formatQuantity($approvalItem->receipt_quantity) }}</td>
                                            <td class="text-center">{{ $approvalItem->unit->name }}</td>
                                            <td class="text-right">{{ formatQuantity($approvalItem->quantity) }}</td>
                                            <td class="text-right">{{ formatQuantity($approvalItem->received_quantity) }}</td>
                                            <td class="text-right">{{ formatQuantity($approvalItem->cut_bill_quantity) }}</td>
                                            <td class="text-right">{{ formatQuantity($approvalItem->remaining_quantity) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                @else
                                    <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td class="align-middle table-head-number-transaction">No</td>
                                            <td class="align-middle table-head-code-transfer-transaction">SKU</td>
                                            <td class="align-middle table-head-name-transaction">Nama Produk</td>
                                            <td class="align-middle table-head-quantity-transaction">Qty</td>
                                            <td class="align-middle table-head-unit-transaction">Unit</td>
                                            <td class="align-middle table-head-price-transaction">Harga</td>
                                            <td class="align-middle table-head-total-transaction">Total</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approval->approvalItems as $key => $approvalItem)
                                            <tr class="text-dark">
                                                <td class="text-center">{{ ++$key }}</td>
                                                <td>{{ $approvalItem->product_sku }} </td>
                                                <td>{{ $approvalItem->product_name }}</td>
                                                <td class="text-right">{{ formatQuantity($approvalItem->quantity) }}</td>
                                                <td>{{ $approvalItem->unit_name }}</td>
                                                <td class="text-right">{{ formatPrice($approvalItem->price) }}</td>
                                                <td class="text-right">{{ formatPrice($approvalItem->total) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @endif
                            </table>
                            <div class="form-group row justify-content-end subtotal-so">
                                <label for="subtotal" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-4 col-sm-4 col-md-2 mr-1">
                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" name="subtotal" id="subtotal" value="{{ formatPrice($approval->subtotal) }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row justify-content-end total-so">
                                <label for="taxAmount" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">PPN</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-4 col-sm-4 col-md-2 mr-1">
                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" name="tax_amount" id="taxAmount" value="{{ formatPrice($approval->tax_amount) }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row justify-content-end grandtotal-so">
                                <label for="grandTotal" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Grand Total</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-4 col-sm-4 col-md-2 mr-1">
                                    <input type="text" class="form-control-plaintext text-bold text-dark text-lg text-right" name="grand_total" id="grandTotal" value="{{ formatPrice($approval->grand_total) }}" readonly>
                                </div>
                            </div>
                            @if(isApprovalSubjectTypeGoodsReceipt($approval->subject_label))
                                <div class="form-group row justify-content-end mt-1">
                                    <label for="paymentAmount" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Pembayaran</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-4 col-sm-4 col-md-2 mr-1">
                                        <input type="text" class="form-control-plaintext text-bold text-danger text-right" id="paymentAmount" value="{{ formatPrice($approval->subject->payment_amount) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end grandtotal-so">
                                    <label for="outstandingAmount" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Sisa Bayar</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-4 col-sm-4 col-md-2 mr-1">
                                        <input type="text" class="form-control-plaintext text-bold text-dark text-lg text-right" id="outstandingAmount" value="{{ formatPrice(getOutstandingAmount($approval->grand_total, $approval->subject->payment_amount)) }}" readonly>
                                    </div>
                                </div>
                            @endif
                            @if(!isApprovalTypeApprovalLimit($approval->type) && !isApprovalTypeCancel($approval->type))
                                <div class="row justify-content-center @if(isApprovalSalesReceiptTransaction($approval->subject_label)) blue-arrow @else blue-arrow-delivery-order @endif">
                                    <i class="fas fa-arrow-down fa-4x text-primary"></i>
                                </div>
                            @endif
                            <hr>
                            @if(!isApprovalTypeApprovalLimit($approval->type) && !isApprovalTypeCancel($approval->type))
                                <div class="container so-update-container text-dark" style="margin-top: 40px">
                                    <div class="row">
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row transaction-date approval-detail-row">
                                                <label for="requestDate" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Tanggal Request</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-4">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="requestDate" id="requestDate" value="{{ formatDate($childData->date, 'd-M-y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row">
                                                <label for="description" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Deskripsi</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-sm-5 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="description" id="description" value="{{ $childData->description }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: -5px">
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row approval-detail-row">
                                                <label for="statusChild" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Status</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="status_child" id="statusChild" value="{{ getApprovalStatusLabel($childData->status) }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        @if(isApprovalSubjectTypeSalesOrder($approval->subject_label))
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group row approval-detail-row">
                                                    <label for="clientLabelChild" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Customer</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-sm-5 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark @if(isDifferenceApprovalItem($childData->customer->name, $approval->client_name)) bg-warning text-bold text-dark approval-difference-amount-section @endif" name="client_label_child" id="clientLabelChild" value="{{ $childData->customer->name ?? '' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if(isApprovalSubjectTypeSalesOrder($approval->subject_label))
                                        <div class="row" style="margin-top: -5px">
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group row transaction-date approval-detail-row">
                                                    <label for="dateChild" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Tanggal Transaksi</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark @if(isDifferenceApprovalItem($childData->subject_date, $approval->subject->date)) bg-warning text-bold text-dark approval-difference-amount-section @endif" name="date_child" id="dateChild" value="{{ formatDate($childData->subject_date, 'd-M-y') }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group row approval-detail-row">
                                                    <label for="marketingChild" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Sales</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-sm-5 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark @if(isDifferenceApprovalItem($childData->marketing->name, $approval->subject->marketing->name)) bg-warning text-bold text-dark approval-difference-amount-section @endif" name="marketing_child" id="marketingChild" value="{{ $childData->marketing->name ?? '' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: -5px">
                                            <div class="col-12 col-lg-6" >
                                                <div class="form-group row approval-detail-row">
                                                    <label for="dueDateChild" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Jatuh Tempo</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-sm-5 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark @if(isDifferenceApprovalItem($childData->tempo, $approval->subject->tempo)) bg-warning text-bold text-dark approval-difference-amount-section @endif" name="due_date_child" id="dueDateChild" value="{{ getDueDate($childData->subject_date, $childData->tempo, 'd-M-y') }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="tablePO">
                                    @if(isApprovalSubjectTypeDeliveryOrder($childData->subject_label))
                                        <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <td class="align-middle table-head-number-transaction">No</td>
                                            <td class="align-middle table-head-code-transfer-transaction">SKU</td>
                                            <td class="align-middle table-head-name-transaction">Nama Produk</td>
                                            <td class="align-middle table-head-quantity-transaction">Qty</td>
                                            <td class="align-middle table-head-unit-transaction">Unit</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($childData->approvalItems as $key => $approvalItem)
                                            <tr class="text-dark">
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td @if(isDifferenceApprovalItem($approvalItem->product->sku, $approvalItems[$key]->product->sku ?? null)) class="bg-warning text-bold text-dark" @endif>
                                                    {{ $approvalItem->product->sku }}
                                                </td>
                                                <td @if(isDifferenceApprovalItem($approvalItem->product->sku, $approvalItems[$key]->product->sku ?? null)) class="bg-warning text-bold text-dark" @endif>
                                                    {{ $approvalItem->product->name }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->quantity, $approvalItems[$key]->quantity ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatQuantity($approvalItem->quantity) }}
                                                </td>
                                                <td class="text-center @if(isDifferenceApprovalItem($approvalItem->unit->name, $approvalItems[$key]->unit->name ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ $approvalItem->unit->name }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    @elseif(isApprovalSubjectTypeGoodsReceipt($childData->subject_label))
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="table-head-number-transaction">No</td>
                                                <td class="table-head-shipping-cost-transaction">SKU</td>
                                                <td>Nama Produk</td>
                                                <td>Qty</td>
                                                <td class="table-head-unit-transaction">Unit</td>
                                                <td class="table-head-price-transaction">Harga</td>
                                                <td class="table-head-price-transaction">Upah</td>
                                                <td class="table-head-price-transaction">Ongkos Kirim</td>
                                                <td class="table-head-price-transaction">Harga Modal</td>
                                                <td class="table-head-shipping-cost-transaction">Total</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($childData->approvalItems as $key => $approvalItem)
                                            <tr class="text-dark">
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td @if(isDifferenceApprovalItem($approvalItem->product->sku, $approvalItems[$key]->product->sku ?? null)) class="bg-warning text-bold text-dark" @endif>
                                                    {{ $approvalItem->product->sku }}
                                                </td>
                                                <td @if(isDifferenceApprovalItem($approvalItem->product->name, $approvalItems[$key]->product->name ?? null)) class="bg-warning text-bold text-dark" @endif>
                                                    {{ $approvalItem->product->name }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->quantity, $approvalItems[$key]->quantity ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatQuantity($approvalItem->quantity) }}
                                                </td>
                                                <td class="text-center @if(isDifferenceApprovalItem($approvalItem->unit->name, $approvalItems[$key]->unit->name ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ $approvalItem->unit->name }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->price, $approvalItems[$key]->price ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatPrice($approvalItem->price) }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->wages, $approvalItems[$key]->wages ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatPrice($approvalItem->wages) }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->shipping_cost, $approvalItems[$key]->shipping_cost ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatPrice($approvalItem->shipping_cost) }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->cost_price, $approvalItems[$key]->cost_price ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatPrice($approvalItem->cost_price) }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->total, $approvalItems[$key]->total ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatPrice($approvalItem->total) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    @else
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="align-middle table-head-number-transaction">No</td>
                                                <td class="align-middle table-head-code-transfer-transaction">SKU</td>
                                                <td class="align-middle table-head-name-transaction">Nama Produk</td>
                                                <td class="align-middle table-head-quantity-transaction">Qty</td>
                                                <td class="align-middle table-head-unit-transaction">Unit</td>
                                                <td class="align-middle table-head-price-transaction">Harga</td>
                                                <td class="align-middle table-head-total-transaction">Total</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($childData->approvalItems as $key => $approvalItem)
                                            <tr class="text-dark">
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td @if(isDifferenceApprovalItem($approvalItem->product_sku, $approvalItems[$key]->product_sku ?? null)) class="bg-warning text-bold text-dark" @endif>
                                                    {{ $approvalItem->product_sku }}
                                                </td>
                                                <td @if(isDifferenceApprovalItem($approvalItem->product_name, $approvalItems[$key]->product_name ?? null)) class="bg-warning text-bold text-dark" @endif>
                                                    {{ $approvalItem->product_name }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->quantity, $approvalItems[$key]->quantity ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatQuantity($approvalItem->quantity) }}
                                                </td>
                                                <td @if(isDifferenceApprovalItem($approvalItem->unit_name, $approvalItems[$key]->unit_name ?? null)) class="bg-warning text-bold text-dark" @endif>
                                                    {{ $approvalItem->unit_name }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->price, $approvalItems[$key]->price ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatPrice($approvalItem->price) }}
                                                </td>
                                                <td class="text-right @if(isDifferenceApprovalItem($approvalItem->total, $approvalItems[$key]->total ?? null)) bg-warning text-bold text-dark @endif">
                                                    {{ formatPrice($approvalItem->total) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    @endif
                                </table>
                                <div class="form-group row justify-content-end subtotal-so">
                                    <label for="subtotalChild" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-4 col-sm-4 col-md-2 mr-1">
                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-danger text-right @if(isDifferenceApprovalItem($childData->subtotal, $approval->subtotal)) bg-warning text-bold text-dark approval-difference-amount-section @endif" name="subtotal_child" id="subtotalChild" value="{{ formatPrice($childData->subtotal) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end total-so">
                                    <label for="taxAmountChild" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">PPN</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-4 col-sm-4 col-md-2 mr-1">
                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-danger text-right @if(isDifferenceApprovalItem($childData->tax_amount, $approval->tax_amount)) bg-warning text-bold text-dark approval-difference-amount-section @endif" name="tax_amount_child" id="taxAmountChild" value="{{ formatPrice($childData->tax_amount) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-end grandtotal-so">
                                    <label for="grandTotalChild" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Grand Total</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-4 col-sm-4 col-md-2 mr-1">
                                        <input type="text" class="form-control-plaintext text-bold text-dark text-lg text-right @if(isDifferenceApprovalItem($childData->grand_total, $approval->grand_total)) bg-warning text-bold text-dark approval-difference-amount-section @endif" name="grand_total_child" id="grandTotalChild" value="{{ formatPrice($childData->grand_total) }}" readonly>
                                    </div>
                                </div>
                                @if(isApprovalSubjectTypeGoodsReceipt($childData->subject_label))
                                    <div class="form-group row justify-content-end mt-1">
                                        <label for="paymentAmountChild" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Pembayaran</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" class="form-control-plaintext text-bold text-danger text-right" id="paymentAmountChild" value="{{ formatPrice($childData->subject->payment_amount) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row justify-content-end grandtotal-so">
                                        <label for="outstandingAmountChild" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Sisa Bayar</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" class="form-control-plaintext text-bold text-dark text-lg text-right @if(isDifferenceApprovalItem(getOutstandingAmount($childData->grand_total, $childData->subject->payment_amount), getOutstandingAmount($approval->grand_total, $approval->subject->payment_amount))) bg-warning text-bold text-dark approval-difference-amount-section @endif" id="outstandingAmountChild" value="{{ formatPrice(getOutstandingAmount($childData->grand_total, $childData->subject->payment_amount)) }}" readonly>
                                        </div>
                                    </div>
                                @endif
                                <hr>
                            @endif
                            <div class="form-row justify-content-center">
                                <div class="col-2">
                                    <a href="{{ url()->previous() }}" class="btn btn-primary btn-block text-bold">Kembali ke Daftar</a>
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
