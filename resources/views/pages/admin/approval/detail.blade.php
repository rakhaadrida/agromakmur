@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Approval</h1>
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
                            <form action="" method="">
                                @csrf
                                <div class="container so-update-container text-dark">
                                    <div class="row">
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row kode-dokumen">
                                                <label for="number" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Transaction Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-4 col-md-3">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="number" id="number" value="{{ $approval->subject->number }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row">
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
                                            <div class="form-group row tanggal-dokumen customer-detail">
                                                <label for="date" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Transaction Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-4">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="date" id="date" value="{{ formatDate($approval->subject->date, 'd-M-y') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        @if(isApprovalSalesTransaction($approval->subject_label))
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group row customer-detail">
                                                    <label for="marketing" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Marketing</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-sm-5 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="marketing" id="marketing" value="{{ $approval->subject->marketing->name }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(isApprovalSubjectTypeGoodsReceipt($approval->subject_label))
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group row customer-detail">
                                                    <label for="warehouse" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Warehouse</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-sm-5 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="warehouse" id="warehouse" value="{{ $approval->subject->warehouse->name }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row" style="margin-top: -5px">
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group row tanggal-dokumen customer-detail" @if(isApprovalTypeCancel($approval->type)) style="margin-top: -20px" @endif>
                                                <label for="status" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Status</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6 col-md-7">
                                                    <input type="text" class="form-control-plaintext col-form-label-sm text-bold @if(isApprovalTypeCancel($approval->type)) bg-warning text-danger @else text-dark @endif" name="status" id="status" value="{{ getApprovalStatusLabel($approval->status) }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        @if(isApprovalSubjectTypeSalesOrder($approval->subject_label))
                                            <div class="col-12 col-lg-6" >
                                                <div class="form-group row customer-detail">
                                                    <label for="dueDate" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Due Date</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-sm-5 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="due_date" id="dueDate" value="{{ getDueDate($approval->subject->date, $approval->subject->tempo, 'd-m-Y') }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                       @endif
                                    </div>
                                    <div class="row" style="margin-top: 5px;">
                                        <div class="col-12 col-lg-6">
                                            @if(isApprovalTypeApprovalLimit($approval->type))
                                                <div class="form-group row customer-detail">
                                                    <label for="limit" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-dark text-right mt-1" style="font-size: 16px">Limit</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-md bg-warning text-danger text-bold text-lg" name="limit" id="limit" value="{{ formatPrice($approval->subject->customer->credit_limit) }}" readonly>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        @if(isApprovalTypeApprovalLimit($approval->type) || isApprovalTypeCancel($approval->type))
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group row customer-detail">
                                                    <label for="description" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Description</label>
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
                                    @if(isApprovalSubjectTypeProductTransfer($appoval->subject_label))
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="table-head-number-transaction">No</td>
                                                <td class="table-head-code-transaction">SKU</td>
                                                <td class="table-head-name-transaction">Product Name</td>
                                                <td class="table-head-unit-transaction">Unit</td>
                                                <td>Source Warehouse</td>
                                                <td>Transferred Qty</td>
                                                <td>Destination Warehouse</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($approval->approvalItems as $key => $approvalItem)
                                                <tr class="text-dark">
                                                    <td class="text-center">{{ ++$key }}</td>
                                                    <td>{{ $approvalItem->product->sku }} </td>
                                                    <td>{{ $approvalItem->product->name }}</td>
                                                    <td>{{ $approvalItem->unit->name }}</td>
                                                    <td class="text-center">{{ $approvalItem->sourceWarehouse->name }}</td>
                                                    <td class="text-right">{{ formatQuantity($approvalItem->quantity) }}</td>
                                                    <td class="text-center">{{ $approvalItem->destinationWarehouse->name }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    @elseif(isApprovalSubjectTypeGoodsReceipt($approval->subject_label))
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td class="table-head-number-transaction">No</td>
                                                <td class="table-head-shipping-cost-transaction">SKU</td>
                                                <td>Product Name</td>
                                                <td>Qty</td>
                                                <td class="table-head-unit-transaction">Unit</td>
                                                <td class="table-head-price-transaction">Price</td>
                                                <td class="table-head-price-transaction">Wages</td>
                                                <td class="table-head-shipping-cost-transaction">Shipping Cost</td>
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
                                                    <td class="text-right">{{ formatPrice($approvalItem->total) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    @else
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <td rowspan="2" class="align-middle table-head-number-transaction">No</td>
                                                <td rowspan="2" class="align-middle table-head-code-transfer-transaction">SKU</td>
                                                <td rowspan="2" class="align-middle table-head-name-transaction">Product Name</td>
                                                <td rowspan="2" class="align-middle table-head-quantity-transaction">Qty</td>
                                                <td colspan="{{ $totalWarehouses }}">Warehouse</td>
                                                <td rowspan="2" class="align-middle table-head-unit-transaction">Unit</td>
                                                <td rowspan="2" class="align-middle table-head-price-transaction">Price</td>
                                                <td rowspan="2" class="align-middle table-head-total-transaction">Total</td>
                                                <td colspan="2">Discount</td>
                                                <td rowspan="2" class="align-middle table-head-total-transaction">Final Amount</td>
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
                                            @foreach($approval->approvalItems as $key => $approvalItem)
                                                <tr class="text-dark">
                                                    <td class="text-center">{{ ++$key }}</td>
                                                    <td>{{ $approvalItem->product->sku }} </td>
                                                    <td>{{ $approvalItem->product->name }}</td>
                                                    <td class="text-right">{{ formatQuantity($approvalItem->quantity) }}</td>
                                                    @foreach($warehouses as $index => $warehouse)
                                                        <td class="text-right">{{ $productWarehouses[$approvalItem->product_id][$warehouse->id] ?? '' }}</td>
                                                    @endforeach
                                                    <td>{{ $approvalItem->unit->name }}</td>
                                                    <td class="text-right">{{ formatPrice($approvalItem->price) }}</td>
                                                    <td class="text-right">{{ formatPrice($approvalItem->total) }}</td>
                                                    <td class="text-right">{{ $approvalItem->discount }}</td>
                                                    <td class="text-right">{{ formatPrice($approvalItem->discount_amount) }}</td>
                                                    <td class="text-right">{{ formatPrice($approvalItem->final_amount) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    @endif
                                </table>
                                @if(isApprovalSubjectTypeGoodsReceipt($approval->subject_label))
                                    <div class="form-group row justify-content-end subtotal-so">
                                        <label for="subtotal" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" name="subtotal" id="subtotal" value="{{ formatPrice($approval->subtotal) }}" readonly>
                                        </div>
                                    </div>
                                @endif
                                @if(isApprovalSubjectTypeSalesOrder($approval->subject_label))
                                    <div class="form-group row justify-content-end subtotal-so">
                                        <label for="total" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Total</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" name="total" id="total" value="{{ formatPrice($approval->subtotal) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row justify-content-end total-so">
                                        <label for="invoiceDiscount" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Invoice Discount</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" name="invoice_discount" id="invoiceDiscount" value="{{ formatPrice($approval->discount_amount) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row justify-content-end total-so">
                                        <label for="subtotal" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" name="subtotal" id="subtotal" value="{{ formatPrice($approval->subtotal - $approval->discount_amount) }}" readonly>
                                        </div>
                                    </div>
                                @endif
                                @if(isApprovalSubjectTypeGoodsReceipt($approval->subject_label) || isApprovalSubjectTypeSalesOrder($approval->subject_label))
                                    <div class="form-group row justify-content-end total-so">
                                        <label for="taxAmount" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Tax Amount</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" name="taxAmount" id="taxAmount" value="{{ formatPrice($approval->tax_amount) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row justify-content-end grandtotal-so">
                                        <label for="grandTotal" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Grand Total</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" class="form-control-plaintext text-bold text-dark text-lg text-right" name="grandTotal" id="grandTotal" value="{{ formatPrice($approval->grand_total) }}" readonly>
                                        </div>
                                    </div>
                                @endif
                                @if(!isApprovalTypeApprovalLimit($approval->type) && !isApprovalTypeCancel($approval->type))
                                    <div class="row justify-content-center blue-arrow" style="margin-top: -80px">
                                        <i class="fas fa-arrow-down fa-4x text-primary"></i>
                                    </div>
                                    <hr>
                                    <div class="container so-update-container text-dark" style="margin-top: 40px">
                                        <div class="row" >
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group row tanggal-dokumen customer-detail">
                                                    <label for="requestDate" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Request Date</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="requestDate" id="requestDate" value="{{ formatDate($childData->date, 'd-M-y') }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group row customer-detail">
                                                    <label for="description" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Description</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-sm-5 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="description" id="description" value="{{ $childData->description }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: -5px">
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group row customer-detail">
                                                    <label for="statusChild" class="col-5 col-sm-4 col-md-3 col-lg-4 form-control-sm text-bold text-right mt-1">Status</label>
                                                    <span class="col-form-label text-bold">:</span>
                                                    <div class="col-6 col-md-7">
                                                        <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" name="status_child" id="statusChild" value="{{ getApprovalStatusLabel($childData->status) }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="tablePO">
                                        @if(isApprovalSubjectTypeGoodsReceipt($approval->subject_label))
                                            <thead class="text-center text-bold text-dark">
                                                <tr>
                                                    <td class="table-head-number-transaction">No</td>
                                                    <td class="table-head-shipping-cost-transaction">SKU</td>
                                                    <td>Product Name</td>
                                                    <td>Qty</td>
                                                    <td class="table-head-unit-transaction">Unit</td>
                                                    <td class="table-head-price-transaction">Price</td>
                                                    <td class="table-head-price-transaction">Wages</td>
                                                    <td class="table-head-shipping-cost-transaction">Shipping Cost</td>
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
                                                    <td class="text-right">{{ formatPrice($approvalItem->total) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        @else
                                            <thead class="text-center text-bold text-dark">
                                                <tr>
                                                    <td rowspan="2" class="align-middle table-head-number-transaction">No</td>
                                                    <td rowspan="2" class="align-middle table-head-code-transfer-transaction">SKU</td>
                                                    <td rowspan="2" class="align-middle table-head-name-transaction">Product Name</td>
                                                    <td rowspan="2" class="align-middle table-head-quantity-transaction">Qty</td>
                                                    <td colspan="{{ $totalWarehouses }}">Warehouse</td>
                                                    <td rowspan="2" class="align-middle table-head-unit-transaction">Unit</td>
                                                    <td rowspan="2" class="align-middle table-head-price-transaction">Price</td>
                                                    <td rowspan="2" class="align-middle table-head-total-transaction">Total</td>
                                                    <td colspan="2">Discount</td>
                                                    <td rowspan="2" class="align-middle table-head-total-transaction">Final Amount</td>
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
                                            @foreach($approval->approvalItems as $key => $approvalItem)
                                                <tr class="text-dark">
                                                    <td class="text-center">{{ ++$key }}</td>
                                                    <td>{{ $approvalItem->product->sku }} </td>
                                                    <td>{{ $approvalItem->product->name }}</td>
                                                    <td class="text-right">{{ formatQuantity($approvalItem->quantity) }}</td>
                                                    @foreach($warehouses as $index => $warehouse)
                                                        <td class="text-right">{{ $productWarehouses[$approvalItem->product_id][$warehouse->id] ?? '' }}</td>
                                                    @endforeach
                                                    <td>{{ $approvalItem->unit->name }}</td>
                                                    <td class="text-right">{{ formatPrice($approvalItem->price) }}</td>
                                                    <td class="text-right">{{ formatPrice($approvalItem->total) }}</td>
                                                    <td class="text-right">{{ $approvalItem->discount }}</td>
                                                    <td class="text-right">{{ formatPrice($approvalItem->discount_amount) }}</td>
                                                    <td class="text-right">{{ formatPrice($approvalItem->final_amount) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        @endif
                                    </table>

                                      @if(($item->tipe == 'Faktur') || ($item->tipe == 'Dokumen'))
                                        <div class="form-group row justify-content-end subtotal-so">
                                          <label for="totalNotPPN" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                                          <span class="col-form-label text-bold">:</span>
                                          <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" name="totalNotPPN" id="totalNotPPN" readonly class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" value="{{ number_format($subtotalUpdate, 0, "", ".") }}"
                                          </div>
                                        </div>
                                      @endif
                                      @if($item->tipe == 'Faktur')
                                        <div class="form-group row justify-content-end total-so">
                                          <label for="ppn" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Diskon Faktur</label>
                                          <span class="col-form-label text-bold">:</span>
                                          <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" name="ppn" id="ppn" readonly class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" value="{{ number_format($item->so->diskon, 0, "", ".") }}">
                                          </div>
                                        </div>
                                        <div class="form-group row justify-content-end total-so">
                                          <label for="ppn" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">Total Sebelum PPN</label>
                                          <span class="col-form-label text-bold">:</span>
                                          <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" name="ppn" id="ppn" readonly class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" value="{{ number_format($subtotalUpdate - $item->so->diskon, 0, "", ".") }}">
                                          </div>
                                        </div>
                                      @endif
                                      @if(($item->tipe == 'Faktur') || ($item->tipe == 'Dokumen'))
                                        <div class="form-group row justify-content-end total-so">
                                          <label for="ppn" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">PPN</label>
                                          <span class="col-form-label text-bold">:</span>
                                          <div class="col-4 col-sm-4 col-md-2 mr-1">
                                            <input type="text" name="ppn" id="ppn" readonly class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" value="0">
                                          </div>
                                        </div>
                                      @endif
                                      @if(($item->tipe == 'Faktur') || ($item->tipe == 'Dokumen'))
                                    <div class="form-group row justify-content-end grandtotal-so">
                                      <label for="grandtotal" class="col-4 col-sm-4 col-md-2 col-form-label text-bold text-right text-dark">@if($itemsUpdate->last()->status == 'LIMIT') Total Tagihan @else Grand Total @endif</label>
                                      <span class="col-form-label text-bold">:</span>
                                      <div class="col-4 col-sm-4 col-md-2 mr-1">
                                        <input type="text" name="grandtotalAkhir{{$item->id_dokumen}}" id="grandtotal" readonly class="form-control-plaintext text-bold @if(($itemsUpdate->last()->status == 'LIMIT') && ($itemsUpdate->last()->status == 'PENDING_BATAL')) bg-warning text-danger @else text-dark @endif text-lg text-right" value="{{ $item->tipe == 'Faktur' ? number_format($subtotalUpdate - $item->so->diskon, 0, "", ".") : number_format($subtotalUpdate, 0, "", ".") }}">
                                      </div>
                                    </div>
                                  @endif
                                        @if($iu->id != $itemsUpdate[$itemsUpdate->count() - 1]->id)
                                      <div class="row justify-content-center" style="margin-top: -80px">
                                        <i class="fas fa-arrow-down fa-4x text-primary"></i>
                                      </div>
                                    @endif
                                    <hr>
                                @endif
                                <div class="form-row justify-content-center">
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <button type="submit" formaction="" formmethod="POST" class="btn btn-success btn-block text-bold">Approve</button>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <button type="submit" formaction="" formmethod="POST" class="btn btn-danger btn-block text-bold">Cancel Revision</button>
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
@endpush
