@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Histori Approval</h1>
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
                        <a class="nav-link nav-link-inactive active" id="salesOrderTab" data-toggle="pill" data-target="#salesOrder" type="button" role="tab" aria-controls="sales-order" aria-selected="true">Sales Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="goodsReceiptTab" data-toggle="pill" data-target="#goodsReceipt" type="button" role="tab" aria-controls="goods-receipt" aria-selected="false">Barang Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="deliveryOrderTab" data-toggle="pill" data-target="#deliveryOrder" type="button" role="tab" aria-controls="delivery-order" aria-selected="false">Surat Jalan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="salesReturnTab" data-toggle="pill" data-target="#salesReturn" type="button" role="tab" aria-controls="sales-return" aria-selected="false">Retur Penjualan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="purchaseReturnTab" data-toggle="pill" data-target="#purchaseReturn" type="button" role="tab" aria-controls="purchase-return" aria-selected="false">Retur Pembelian</a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <div class="card show card-tabs">
                        <div class="card-body">
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="salesOrder" role="tabpanel" aria-labelledby="salesOrderTab">
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTableSalesOrder">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <th class="align-middle th-number-transaction-index">No</th>
                                                <th class="align-middle th-approval-number">Nomor SO</th>
                                                <th class="align-middle th-approval-date">Tanggal SO</th>
                                                <th class="align-middle th-approval-date">Tanggal Request</th>
                                                <th class="align-middle th-approval-branch">Cabang</th>
                                                <th class="align-middle">Customer</th>
                                                <th class="align-middle th-approval-type">Tipe</th>
                                                <th class="align-middle th-approval-description">Deskripsi</th>
                                                <th class="align-middle th-approval-admin">Admin</th>
                                                <th class="align-middle th-approval-action">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($approvals as $key => $approval)
                                                <tr class="text-dark">
                                                    <td class="align-middle text-center">{{ ++$key }}</td>
                                                    <td class="align-middle">
                                                        <a href="{{ route('sales-orders.detail', $approval->subject_id) }}" class="btn btn-sm btn-link text-bold">
                                                            {{ $approval->subject->number }}
                                                        </a>
                                                    </td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($approval->subject->date, 'Ymd') }}">{{ formatDateIso($approval->subject->date, 'DD-MMM-YY')  }}</td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($approval->date, 'Ymd') }}">{{ formatDateIso($approval->date, 'DD-MMM-YY')  }}</td>
                                                    <td class="align-middle">{{ $approval->subject->branch->name }}</td>
                                                    <td class="align-middle">{{ $approval->subject->customer->name }}</td>
                                                    <td class="align-middle text-center">{{ getApprovalTypeLabel($approval->type) }}</td>
                                                    <td class="align-middle text-center">{{ $approval->description }}</td>
                                                    <td class="align-middle text-center">{{ $approval->updated_user_name }}</td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('approvals.detail', $approval->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-fw fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="goodsReceipt" role="tabpanel" aria-labelledby="goodsReceiptTab">
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTableGoodsReceipt">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <th class="align-middle th-number-transaction-index">No</th>
                                                <th class="align-middle th-approval-number">Nomor BM</th>
                                                <th class="align-middle th-approval-date">Tanggal BM</th>
                                                <th class="align-middle th-approval-date">Tanggal Request</th>
                                                <th class="align-middle th-approval-branch">Cabang</th>
                                                <th class="align-middle">Supplier</th>
                                                <th class="align-middle th-approval-type">Tipe</th>
                                                <th class="align-middle th-approval-description">Deskripsi</th>
                                                <th class="align-middle th-approval-admin">Admin</th>
                                                <th class="align-middle th-approval-action">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemGoodsReceipt">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="deliveryOrder" role="tabpanel" aria-labelledby="deliveryOrderTab">
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTableDeliveryOrder">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <th class="align-middle th-number-transaction-index">No</th>
                                                <th class="align-middle th-approval-number">Nomor SJ</th>
                                                <th class="align-middle th-approval-date">Tanggal SJ</th>
                                                <th class="align-middle th-approval-date">Tanggal Request</th>
                                                <th class="align-middle th-approval-branch">Cabang</th>
                                                <th class="align-middle">Customer</th>
                                                <th class="align-middle th-approval-type">Tipe</th>
                                                <th class="align-middle th-approval-description">Deskripsi</th>
                                                <th class="align-middle th-approval-admin">Admin</th>
                                                <th class="align-middle th-approval-action">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemDeliveryOrder">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="salesReturn" role="tabpanel" aria-labelledby="salesReturnTab">
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTableSalesReturn">
                                        <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-number-transaction-index">No</th>
                                            <th class="align-middle th-approval-number">Nomor Retur</th>
                                            <th class="align-middle th-approval-date">Tanggal Retur</th>
                                            <th class="align-middle th-approval-date">Tanggal Request</th>
                                            <th class="align-middle th-approval-branch">Cabang</th>
                                            <th class="align-middle">Customer</th>
                                            <th class="align-middle th-approval-type">Tipe</th>
                                            <th class="align-middle th-approval-description">Deskripsi</th>
                                            <th class="align-middle th-approval-admin">Admin</th>
                                            <th class="align-middle th-approval-action">Aksi</th>
                                        </tr>
                                        </thead>
                                        <tbody id="itemSalesReturn">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="purchaseReturn" role="tabpanel" aria-labelledby="purchaseReturnTab">
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTablePurchaseReturn">
                                        <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-number-transaction-index">No</th>
                                            <th class="align-middle th-approval-number">Nomor Retur</th>
                                            <th class="align-middle th-approval-date">Tanggal Retur</th>
                                            <th class="align-middle th-approval-date">Tanggal Request</th>
                                            <th class="align-middle th-approval-branch">Cabang</th>
                                            <th class="align-middle">Supplier</th>
                                            <th class="align-middle th-approval-type">Tipe</th>
                                            <th class="align-middle th-approval-description">Deskripsi</th>
                                            <th class="align-middle th-approval-admin">Admin</th>
                                            <th class="align-middle th-approval-action">Aksi</th>
                                        </tr>
                                        </thead>
                                        <tbody id="itemPurchaseReturn">
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
        $.fn.dataTable.ext.order['dom-data-sort'] = function (settings, col) {
            return this.api()
                .column(col, { order: 'index' })
                .nodes()
                .map(function (td) {
                    return $(td).data('sort');
                });
        };

        let datatableSalesOrder = $('#dataTableSalesOrder').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</span>`
            },
            "order": [
                [3, "desc"]
            ],
            "columnDefs": [
                {
                    targets: [0, 4, 6, 7, 8, 9],
                    orderable: false
                }
            ],
        });

        let datatableGoodsReceipt = $('#dataTableGoodsReceipt').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</span>`
            },
            "order": [
                [3, "desc"]
            ],
            "columnDefs": [
                {
                    targets: [2, 3],
                    orderDataType: 'dom-data-sort'
                },
                {
                    targets: [0, 4, 6, 7, 8, 9],
                    orderable: false
                }
            ],
        });

        let datatableDeliveryOrder = $('#dataTableDeliveryOrder').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</span>`
            },
            "order": [
                [3, "desc"]
            ],
            "columnDefs": [
                {
                    targets: [2, 3],
                    orderDataType: 'dom-data-sort'
                },
                {
                    targets: [0, 4, 6, 7, 8, 9],
                    orderable: false
                }
            ],
        });

        let datatableSalesReturn = $('#dataTableSalesReturn').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</span>`
            },
            "order": [
                [3, "desc"]
            ],
            "columnDefs": [
                {
                    targets: [2, 3],
                    orderDataType: 'dom-data-sort'
                },
                {
                    targets: [0, 4, 6, 7, 8, 9],
                    orderable: false
                }
            ],
        });

        let datatablePurchaseReturn = $('#dataTablePurchaseReturn').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">Tidak Ada Data</span>`
            },
            "order": [
                [3, "desc"]
            ],
            "columnDefs": [
                {
                    targets: [2, 3],
                    orderDataType: 'dom-data-sort'
                },
                {
                    targets: [0, 4, 6, 7, 8, 9],
                    orderable: false
                }
            ],
        });

        $(document).ready(function() {
            $('#goodsReceiptTab').on('click', function (e) {
                e.preventDefault();

                let table = $('#itemGoodsReceipt');
                if(table.find('.item-row').length === 0) {
                    displayApprovalData(table, 'goods-receipts', 10, datatableGoodsReceipt);
                }
            });

            $('#deliveryOrderTab').on('click', function (e) {
                e.preventDefault();

                let table = $('#itemDeliveryOrder');
                if(table.find('.item-row').length === 0) {
                    displayApprovalData(table, 'delivery-orders', 10, datatableDeliveryOrder);
                }
            });

            $('#salesReturnTab').on('click', function (e) {
                e.preventDefault();

                let table = $('#itemSalesReturn');
                if(table.find('.item-row').length === 0) {
                    displayApprovalData(table, 'sales-returns', 10, datatableSalesReturn);
                }
            });

            $('#purchaseReturnTab').on('click', function (e) {
                e.preventDefault();

                let table = $('#itemPurchaseReturn');
                if(table.find('.item-row').length === 0) {
                    displayApprovalData(table, 'purchase-returns', 10, datatablePurchaseReturn);
                }
            });

            function displayApprovalData(table, subject, colspan, datatable) {
                $.ajax({
                    url: '{{ route('approvals.index-history-ajax') }}',
                    type: 'GET',
                    data: {
                        subject: subject,
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        table.empty();

                        let loadingRow = loadingItemRow(colspan);
                        table.append(loadingRow);
                    },
                    success: function(data) {
                        let approvals = data.data;
                        table.empty();

                        if(approvals.length === 0) {
                            datatable.clear();
                            datatable.draw(false);
                        } else {
                            let rowNumber = 1;

                            let newRow;
                            $.each(approvals, function(index, item) {
                                switch (subject) {
                                    case 'goods-receipts':
                                        newRow = goodsReceiptItemRow(rowNumber, item);
                                        break;
                                    case 'delivery-orders':
                                        newRow = deliveryOrderItemRow(rowNumber, item);
                                        break;
                                    case 'sales-returns':
                                        newRow = salesReturnItemRow(rowNumber, item);
                                        break;
                                    case 'purchase-returns':
                                        newRow = purchaseReturnItemRow(rowNumber, item);
                                        break;
                                    default:
                                        return;
                                }

                                table.append(newRow);
                                rowNumber++;
                            });

                            if (datatable) {
                                datatable.clear();

                                if (approvals.length > 0) {
                                    datatable.rows.add(table.find('tr'));
                                }

                                datatable.draw(false);
                            }

                            datatable.draw(false);
                        }
                    },
                })
            }

            function goodsReceiptItemRow(rowNumber, item) {
                let baseUrl = `{{ route('goods-receipts.detail', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.subject_id);
                let urlEdit = `{{ route('approvals.detail', '') }}` + '/' + item.id;

                return `
                    <tr class="text-dark item-row">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td class="align-middle">
                            <a href="${urlDetail}" class="btn btn-sm btn-link text-bold">
                                ${item.subject.number}
                            </a>
                        </td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.subject.date, 'Ymd')}">${formatDate(item.subject.date, 'd-M-y')}</td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.date, 'Ymd')}">${formatDate(item.date, 'd-M-y')}</td>
                        <td class="align-middle">${item.subject.branch.name}</td>
                        <td class="align-middle">${item.subject.supplier.name}</td>
                        <td class="align-middle text-center">${getApprovalTypeLabel(item.type)}</td>
                        <td class="align-middle text-center">${item.description}</td>
                        <td class="align-middle text-center">${item.updated_user_name}</td>
                        <td class="align-middle text-center">
                            <a href="${urlEdit}" class="btn btn-sm btn-info">
                                <i class="fas fa-fw fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            }

            function deliveryOrderItemRow(rowNumber, item) {
                let baseUrl = `{{ route('delivery-orders.detail', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.subject_id);
                let urlEdit = `{{ route('approvals.detail', '') }}` + '/' + item.id;

                return `
                    <tr class="text-dark item-row">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td class="align-middle">
                            <a href="${urlDetail}" class="btn btn-sm btn-link text-bold">
                                ${item.subject.number}
                            </a>
                        </td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.subject.date, 'Ymd')}">${formatDate(item.subject.date, 'd-M-y')}</td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.date, 'Ymd')}">${formatDate(item.date, 'd-M-y')}</td>
                        <td class="align-middle">${item.subject.branch.name}</td>
                        <td class="align-middle">${item.subject.customer.name}</td>
                        <td class="align-middle text-center">${getApprovalTypeLabel(item.type)}</td>
                        <td class="align-middle text-center">${item.description}</td>
                        <td class="align-middle text-center">${item.updated_user_name}</td>
                        <td class="align-middle text-center">
                            <a href="${urlEdit}" class="btn btn-sm btn-info">
                                <i class="fas fa-fw fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            }

            function salesReturnItemRow(rowNumber, item) {
                let baseUrl = `{{ route('sales-returns.show', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.subject_id);
                let urlEdit = `{{ route('approvals.detail', '') }}` + '/' + item.id;

                return `
                    <tr class="text-dark item-row">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td class="align-middle">
                            <a href="${urlDetail}" class="btn btn-sm btn-link text-bold">
                                ${item.subject.number}
                            </a>
                        </td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.subject.date, 'Ymd')}">${formatDate(item.subject.date, 'd-M-y')}</td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.date, 'Ymd')}">${formatDate(item.date, 'd-M-y')}</td>
                        <td class="align-middle">${item.subject.sales_order.branch.name}</td>
                        <td class="align-middle">${item.subject.customer.name}</td>
                        <td class="align-middle text-center">${getApprovalTypeLabel(item.type)}</td>
                        <td class="align-middle text-center">${item.description}</td>
                        <td class="align-middle text-center">${item.user_name}</td>
                        <td class="align-middle text-center">
                            <a href="${urlEdit}" class="btn btn-sm btn-info">
                                <i class="fas fa-fw fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            }

            function purchaseReturnItemRow(rowNumber, item) {
                let baseUrl = `{{ route('purchase-returns.show', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.subject_id);
                let urlEdit = `{{ route('approvals.detail', '') }}` + '/' + item.id;

                return `
                    <tr class="text-dark item-row">
                        <td class="align-middle text-center">${rowNumber}</td>
                        <td class="align-middle">
                            <a href="${urlDetail}" class="btn btn-sm btn-link text-bold">
                                ${item.subject.number}
                            </a>
                        </td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.subject.date, 'Ymd')}">${formatDate(item.subject.date, 'd-M-y')}</td>
                        <td class="align-middle text-center" data-sort="${formatDate(item.date, 'Ymd')}">${formatDate(item.date, 'd-M-y')}</td>
                        <td class="align-middle">${item.subject.goods_receipt.branch.name}</td>
                        <td class="align-middle">${item.subject.supplier.name}</td>
                        <td class="align-middle text-center">${getApprovalTypeLabel(item.type)}</td>
                        <td class="align-middle text-center">${item.description}</td>
                        <td class="align-middle text-center">${item.user_name}</td>
                        <td class="align-middle text-center">
                            <a href="${urlEdit}" class="btn btn-sm btn-info">
                                <i class="fas fa-fw fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
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

            function formatDate(dateStr, format = 'd-M-y') {
                const date = new Date(dateStr);

                if (format === 'Ymd') {
                    return date.toISOString().split('T')[0].replace(/-/g, '');
                } else if (format === 'd-M-y') {
                    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' });
                }

                return dateStr;
            }

            function getApprovalTypeLabel(type) {
                const labels = {
                    'EDIT': 'Ubah',
                    'CANCEL': 'Batal',
                    'APPROVAL_LIMIT': 'Approval Limit'
                };

                return labels[type];
            }
        });
    </script>
@endpush
