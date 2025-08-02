@extends('layouts.admin')

@push('addon-style')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Product Transfer</h1>
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
                            <form action="" id="form">
                                <div class="container so-update-container ">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group row">
                                                <label for="number" class="col-5 text-right mt-2">Number</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-6">
                                                    <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="number" value="{{ $productTransfer->number }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group row">
                                                <label for="status" class="col-3 text-right text-bold mt-2">Status</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-7">
                                                    <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="status" value="{{ getProductTransferStatusLabel($productTransfer->status) }}" >
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
                                                    <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="date" value="{{ formatDate($productTransfer->date, 'd-m-Y') }}" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group row detail-po-information-row">
                                                <label for="user" class="col-3 text-right text-bold mt-2">Admin</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-7">
                                                    <input type="text" readonly class="form-control-plaintext text-bold text-dark" id="user" value="{{ $productTransfer->user->username }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
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
                                        @foreach($productTransferItems as $key => $productTransferItem)
                                            <tr class="text-dark">
                                                <td class="text-center">{{ ++$key }}</td>
                                                <td>{{ $productTransferItem->product->sku }} </td>
                                                <td>{{ $productTransferItem->product->name }}</td>
                                                <td>{{ $productTransferItem->unit->name }}</td>
                                                <td class="text-center">{{ $productTransferItem->sourceWarehouse->name }}</td>
                                                <td class="text-right">{{ formatQuantity($productTransferItem->quantity) }}</td>
                                                <td class="text-center">{{ $productTransferItem->destinationWarehouse->name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                        <a href="" class="btn btn-danger btn-block text-bold"  data-toggle="modal" data-target="#modalCancelTransfer">Cancel Transfer</a>
                                    </div>
                                    <div class="col-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Back to List</a>
                                    </div>
                                </div>

                                <div class="modal" id="modalCancelTransfer" tabindex="-1" role="dialog" aria-labelledby="modalCancelTransfer" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" class="h2 text-bold">&times;</span>
                                                </button>
                                                <h4 class="modal-title">Cancel Product Transfer - {{ $productTransfer->number }}</h4>
                                            </div>
                                            <div class="modal-body">
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
                                            </div>
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
    <script type="text/javascript">
        $(document).ready(function() {
            const modalCancelTransfer = $('#modalCancelTransfer');

            modalCancelTransfer.on('show.bs.modal', function (e) {
                $('#description').attr('required', true);
            })

            modalCancelTransfer.on('hide.bs.modal', function (e) {
                $('#description').removeAttr('required');
            })

            $('#btnSubmit').on('click', function(event) {
                event.preventDefault();

                let checkForm = document.getElementById('form').checkValidity();
                if(!checkForm) {
                    document.getElementById('form').reportValidity();
                    return false;
                }

                $('#form').submit();
            });
        });
    </script>
@endpush
