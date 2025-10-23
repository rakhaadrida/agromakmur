@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Purchase Recap</h1>
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
                <ul class="nav nav-tabs" id="tabHeader" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive active" id="productTab" data-toggle="pill" data-target="#product" type="button" role="tab" aria-controls="product" aria-selected="true">By Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="supplierTab" data-toggle="pill" data-target="#supplier" type="button" role="tab" aria-controls="supplier" aria-selected="false">By Supplier</a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <div class="card show card-tabs">
                        <div class="card-body">
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="product" role="tabpanel" aria-labelledby="productTab">
                                    <form action="{{ route('report.purchase-recap.index') }}" method="GET" id="form">
                                        <div class="container so-container">
                                            <div class="form-group row justify-content-center">
                                                <label for="startDate" class="col-auto col-form-label text-bold">Start Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" required>
                                                </div>
                                                <label for="finalDate" class="col-auto col-form-label text-bold ">up to</label>
                                                <div class="col-2">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}">
                                                </div>
                                                <div class="col-1 mt-1 main-transaction-button">
                                                    <button type="submit" class="btn btn-primary btn-sm btn-block text-bold">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                <h4 class="text-bold text-dark">Purchase Recap By Product ({{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}) </h4>
                                            </div>
                                            <div class="row justify-content-center mb-2">
                                                <h6 class="text-dark">Report Time : {{ $reportDate }}</h6>
                                            </div>
                                            <div class="row justify-content-end product-history-detail-export-button">
                                                <div class="col-2 product-history-col">
                                                    <input type="hidden" name="subject" value="product">
                                                    <button type="submit" formaction="{{ route('report.purchase-recap.export') }}" formmethod="GET" class="btn btn-success btn-block text-bold">Export Excel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm" id="dataTableProduct">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <th class="align-middle th-sales-recap-number">No</th>
                                                <th class="align-middle th-sales-recap-product-sku">Product SKU</th>
                                                <th class="align-middle">Product Name</th>
                                                <th class="align-middle text-center th-sales-recap-invoice-count">Invoice Count</th>
                                                <th class="align-middle text-center th-sales-recap-quantity">Total Quantity</th>
                                                <th class="align-middle th-sales-recap-unit">Unit</th>
                                                <th class="align-middle text-center th-sales-recap-total">Grand Total</th>
                                                <th class="align-middle th-sales-recap-action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-bold text-dark" id="itemProduct">
                                            @foreach ($purchaseItems as $index => $purchaseItem)
                                                <tr>
                                                    <td class="align-middle text-center">{{ $index + 1 }}</td>
                                                    <td class="align-middle text-center">{{ $purchaseItem->product_sku }}</td>
                                                    <td class="align-middle">{{ $purchaseItem->product_name }}</td>
                                                    <td class="align-middle text-right">{{ $purchaseItem->invoice_count }}</td>
                                                    <td class="align-middle text-right">{{ $purchaseItem->total_quantity }}</td>
                                                    <td class="align-middle text-center">{{ $purchaseItem->unit_name }}</td>
                                                    <td class="align-middle text-right">{{ $purchaseItem->grand_total }}</td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('report.purchase-recap.show', ['purchase_recap' => $purchaseItem->id, 'subject' => 'products', 'start_date' => $startDate, 'final_date' => $finalDate]) }}" class="btn btn-sm btn-info text-bold">
                                                            Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="supplier" role="tabpanel" aria-labelledby="supplierTab">
                                    <form action="{{ route('report.purchase-recap.index') }}" method="GET" id="form">
                                        <div class="container so-container">
                                            <div class="form-group row justify-content-center">
                                                <label for="startDate" class="col-auto col-form-label text-bold">Start Date</label>
                                                <span class="col-form-label text-bold">:</span>
                                                <div class="col-2">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" required>
                                                </div>
                                                <label for="finalDate" class="col-auto col-form-label text-bold ">up to</label>
                                                <div class="col-2">
                                                    <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}">
                                                </div>
                                                <div class="col-1 mt-1 main-transaction-button">
                                                    <button type="submit" class="btn btn-primary btn-sm btn-block text-bold">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                <h4 class="text-bold text-dark">Sales Recap By Supplier ({{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}) </h4>
                                            </div>
                                            <div class="row justify-content-center mb-2">
                                                <h6 class="text-dark">Report Time : {{ $reportDate }}</h6>
                                            </div>
                                            <div class="row justify-content-end product-history-detail-export-button">
                                                <div class="col-2 product-history-col">
                                                    <input type="hidden" name="subject" value="supplier">
                                                    <button type="submit" formaction="{{ route('report.purchase-recap.export') }}" formmethod="GET" class="btn btn-success btn-block text-bold">Export Excel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm" id="dataTableSupplier">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <th class="align-middle th-sales-recap-number">No</th>
                                                <th class="align-middle">Supplier</th>
                                                <th class="align-middle text-center th-sales-recap-invoice-count">Invoice Count</th>
                                                <th class="align-middle text-center th-sales-recap-subtotal">Subtotal</th>
                                                <th class="align-middle text-center th-sales-recap-subtotal">Tax Amount</th>
                                                <th class="align-middle text-center th-sales-recap-grand-total">Grand Total</th>
                                                <th class="align-middle th-sales-recap-action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-bold text-dark" id="itemSupplier">
                                        </tbody>
                                    </table>
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
    <script src="{{ url('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $.fn.datepicker.dates['id'] = {
            days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            daysShort: ['Mgu', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            daysMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            today: 'Hari Ini',
            clear: 'Kosongkan'
        };

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'id',
        });

        $(document).ready(function() {
            let datatableProduct = $('#dataTableProduct').DataTable({
                "responsive": true,
                "autoWidth": false,
                "pageLength": 25,
                "order": [
                    [2, 'asc']
                ],
                columns: [
                    { data: 'rowNumber', className: 'align-middle text-center' },
                    { data: 'product_sku', className: 'align-middle text-center' },
                    { data: 'product_name', className: 'align-middle' },
                    { data: 'invoice_count', className: 'align-middle text-right' },
                    { data: 'total_quantity', className: 'align-middle text-right' },
                    { data: 'unit_name', className: 'align-middle text-center' },
                    { data: 'grand_total', className: 'align-middle text-right' },
                    { data: 'action', className: 'align-middle text-center' }
                ],
                "columnDefs": [
                    {
                        targets: [0, 5, 7],
                        orderable: false
                    },
                    {
                        targets: [3, 4, 6],
                        render: function (data, type, row) {
                            if (type === 'display') {
                                return formatQuantity(data || 0);
                            }
                            return data;
                        }
                    }
                ],
                "language": {
                    "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">No data available</span>`
                },
            });

            let datatableSupplier = $('#dataTableSupplier').DataTable({
                "responsive": true,
                "autoWidth": false,
                "pageLength": 25,
                "order": [
                    [1, 'asc']
                ],
                columns: [
                    { data: 'rowNumber', className: 'align-middle text-center' },
                    { data: 'supplier_name', className: 'align-middle' },
                    { data: 'invoice_count', className: 'align-middle text-right' },
                    { data: 'subtotal', className: 'align-middle text-right' },
                    { data: 'tax_amount', className: 'align-middle text-right' },
                    { data: 'grand_total', className: 'align-middle text-right' },
                    { data: 'action', className: 'align-middle text-center' }
                ],
                "columnDefs": [
                    {
                        targets: [0, 6],
                        orderable: false
                    },
                    {
                        targets: [2, 3, 4, 5],
                        render: function (data, type, row) {
                            if (type === 'display') {
                                return formatQuantity(data || 0);
                            }
                            return data;
                        }
                    }
                ],
                "language": {
                    "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">No data available</span>`
                },
            });

            let productTab = $('#productTab');
            let supplierTab = $('#supplierTab');

            productTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemProduct');
                if(table.find('.item-row').length === 0) {
                    displayPurchaseItemData(table, 'products', 8, productTab, datatableProduct);
                }
            });

            supplierTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemSupplier');
                if(table.find('.item-row').length === 0) {
                    displayPurchaseItemData(table, 'suppliers', 7, supplierTab, datatableSupplier);
                }
            });

            function displayPurchaseItemData(table, subject, colspan, tabItem, datatable) {
                $.ajax({
                    url: '{{ route('report.purchase-recap.index-ajax') }}',
                    type: 'GET',
                    data: {
                        start_date: '{{ $startDate }}',
                        final_date: '{{ $finalDate }}',
                        subject: subject,
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        table.empty();

                        let loadingRow = loadingItemRow(colspan);
                        table.append(loadingRow);
                    },
                    success: function(data) {
                        let purchaseItems = data.data;
                        table.empty();

                        if (!purchaseItems || purchaseItems.length === 0) {
                            datatable.clear().draw();
                            return;
                        }

                        let rows = [];
                        $.each(purchaseItems, function(index, item) {
                            if (subject === 'products') {
                                let detailUrl = '{{ route('report.purchase-recap.show', ['purchase_recap' => ':id', 'subject' => 'products', 'start_date' => $startDate, 'final_date' => $finalDate]) }}'.replace(':id', item.id);

                                rows.push({
                                    rowNumber: index + 1,
                                    product_sku: item.product_sku,
                                    product_name: item.product_name,
                                    invoice_count: item.invoice_count,
                                    total_quantity: item.total_quantity,
                                    unit_name: item.unit_name,
                                    grand_total: item.grand_total,
                                    action: `<a href="${detailUrl}" class="btn btn-sm btn-info">Detail</a>`
                                });
                            } else if (subject === 'suppliers') {
                                let detailUrl = '{{ route('report.purchase-recap.show', ['purchase_recap' => ':id', 'subject' => 'suppliers', 'start_date' => $startDate, 'final_date' => $finalDate]) }}'.replace(':id', item.id);

                                rows.push({
                                    rowNumber: index + 1,
                                    supplier_name: item.supplier_name,
                                    invoice_count: item.invoice_count,
                                    subtotal: item.subtotal,
                                    tax_amount: item.tax_amount,
                                    grand_total: item.grand_total,
                                    action: `<a href="${detailUrl}" class="btn btn-sm btn-info">Detail</a>`
                                });
                            }
                        });

                        datatable.clear().rows.add(rows).draw();
                    },
                })
            }

            function loadingItemRow(colspan) {
                return `
                    <tr>
                        <td colspan="${colspan}" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                        </td>
                    </tr>
                `;
            }

            function formatQuantity(quantity) {
                return new Intl.NumberFormat('id').format(quantity);
            }
        });
    </script>
@endpush
