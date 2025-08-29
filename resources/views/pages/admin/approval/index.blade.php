@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Approval List</h1>
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
                        <a class="nav-link nav-link-inactive active" id="salesOrderTab" data-toggle="pill" data-target="#salesOrder" type="button" role="tab" aria-controls="sales-order" aria-selected="true">Sales Order
                            @if($mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER] ?? 0)
                                <span class="notification-badge">{{ $mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER] ?? 0 }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="goodsReceiptTab" data-toggle="pill" data-target="#goodsReceipt" type="button" role="tab" aria-controls="goods-receipt" aria-selected="false">Goods Receipt
                            @if($mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT] ?? 0)
                                <span class="notification-badge">{{ $mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="deliveryOrderTab" data-toggle="pill" data-target="#deliveryOrder" type="button" role="tab" aria-controls="delivery-order" aria-selected="false">Delivery Order
                            @if($mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER] ?? 0)
                                <span class="notification-badge">{{ $mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="productTransferTab" data-toggle="pill" data-target="#productTransfer" type="button" role="tab" aria-controls="product-transfer" aria-selected="false">Product Transfer
                            @if($mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_PRODUCT_TRANSFER] ?? 0)
                                <span class="notification-badge">{{ $mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_PRODUCT_TRANSFER] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-inactive" id="salesReturnTab" data-toggle="pill" data-target="#salesReturn" type="button" role="tab" aria-controls="sales-return" aria-selected="false">Sales Return
                            @if($mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_SALES_RETURN] ?? 0)
                                <span class="notification-badge">{{ $mapCountApprovalBySubject[\App\Utilities\Constant::APPROVAL_SUBJECT_TYPE_SALES_RETURN] }}</span>
                            @endif
                        </a>
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
                                                <th class="align-middle th-code-transaction-index">Order Number</th>
                                                <th class="align-middle th-code-transaction-index">Order Date</th>
                                                <th class="align-middle th-status-transaction-index">Request Date</th>
                                                <th class="align-middle th-name-transaction-index">Customer</th>
                                                <th class="align-middle th-status-transaction-index">Type</th>
                                                <th class="align-middle th-warehouse-transaction-index">Description</th>
                                                <th class="align-middle th-code-transaction-index">Admin</th>
                                                <th class="align-middle th-code-transaction-index">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemSalesOrder">
                                            @foreach ($approvals as $key => $approval)
                                                <tr class="text-dark">
                                                    <td class="align-middle text-center">{{ ++$key }}</td>
                                                    <td class="align-middle">
                                                        <a href="{{ route('sales-orders.detail', $approval->subject_id) }}" class="btn btn-sm btn-link text-bold">
                                                            {{ $approval->subject->number }}
                                                        </a>
                                                    </td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($approval->subject->date, 'Ymd') }}">{{ formatDate($approval->subject->date, 'd-M-y')  }}</td>
                                                    <td class="align-middle text-center" data-sort="{{ formatDate($approval->date, 'Ymd') }}">{{ formatDate($approval->date, 'd-M-y')  }}</td>
                                                    <td class="align-middle">{{ $approval->subject->customer->name }}</td>
                                                    <td class="align-middle text-center">{{ getApprovalTypeLabel($approval->type) }}</td>
                                                    <td class="align-middle text-center">{{ $approval->description }}</td>
                                                    <td class="align-middle text-center">{{ $approval->user_name }}</td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('approvals.show', $approval->id) }}" class="btn btn-sm btn-info">
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
                                                <th class="align-middle th-code-transaction-index">Receipt Number</th>
                                                <th class="align-middle th-code-transaction-index">Receipt Date</th>
                                                <th class="align-middle th-status-transaction-index">Request Date</th>
                                                <th class="align-middle th-name-transaction-index">Supplier</th>
                                                <th class="align-middle th-status-transaction-index">Type</th>
                                                <th class="align-middle th-warehouse-transaction-index">Description</th>
                                                <th class="align-middle th-code-transaction-index">Admin</th>
                                                <th class="align-middle th-code-transaction-index">Action</th>
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
                                                <th class="align-middle th-code-transaction-index">Delivery Number</th>
                                                <th class="align-middle th-code-transaction-index">Delivery Date</th>
                                                <th class="align-middle th-status-transaction-index">Request Date</th>
                                                <th class="align-middle th-name-transaction-index">Customer</th>
                                                <th class="align-middle th-status-transaction-index">Type</th>
                                                <th class="align-middle th-warehouse-transaction-index">Description</th>
                                                <th class="align-middle th-code-transaction-index">Admin</th>
                                                <th class="align-middle th-code-transaction-index">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemDeliveryOrder">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="productTransfer" role="tabpanel" aria-labelledby="productTransferTab">
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTableProductTransfer">
                                        <thead class="text-center text-bold text-dark">
                                            <tr>
                                                <th class="align-middle th-number-transaction-index">No</th>
                                                <th class="align-middle th-code-transaction-index">Transfer Number</th>
                                                <th class="align-middle th-code-transaction-index">Transfer Date</th>
                                                <th class="align-middle th-status-transaction-index">Request Date</th>
                                                <th class="align-middle th-status-transaction-index">Type</th>
                                                <th class="align-middle th-warehouse-transaction-index">Description</th>
                                                <th class="align-middle th-code-transaction-index">Admin</th>
                                                <th class="align-middle th-code-transaction-index">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemProductTransfer">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="salesReturn" role="tabpanel" aria-labelledby="salesReturnTab">
                                    <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTableSalesReturn">
                                        <thead class="text-center text-bold text-dark">
                                        <tr>
                                            <th class="align-middle th-number-transaction-index">No</th>
                                            <th class="align-middle th-code-transaction-index">Return Number</th>
                                            <th class="align-middle th-code-transaction-index">Return Date</th>
                                            <th class="align-middle th-status-transaction-index">Request Date</th>
                                            <th class="align-middle th-name-transaction-index">Customer</th>
                                            <th class="align-middle th-status-transaction-index">Type</th>
                                            <th class="align-middle th-warehouse-transaction-index">Description</th>
                                            <th class="align-middle th-code-transaction-index">Admin</th>
                                            <th class="align-middle th-code-transaction-index">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="itemSalesReturn">
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
        let datatableSalesOrder = $('#dataTableSalesOrder').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">No data available</span>`
            },
        });

        let datatableGoodsReceipt = $('#dataTableGoodsReceipt').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">No data available</span>`
            },
        });

        let datatableDeliveryOrder = $('#dataTableDeliveryOrder').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">No data available</span>`
            },
        });

        let datatableProductTransfer = $('#dataTableProductTransfer').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">No data available</span>`
            },
        });

        let datatableSalesReturn = $('#dataTableSalesReturn').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "emptyTable": `<span class="text-center text-bold text-dark h4 py-2">No data available</span>`
            },
        });

        $(document).ready(function() {
            let salesOrderTab = $('#salesOrderTab');
            let goodsReceiptTab = $('#goodsReceiptTab');
            let deliveryOrderTab = $('#deliveryOrderTab');
            let productTransferTab = $('#productTransferTab');
            let salesReturnTab = $('#salesReturnTab');

            salesOrderTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemSalesOrder');
                if(table.find('.item-row').length === 0) {
                    displayApprovalData(table, 'sales-orders', 9, salesOrderTab, datatableSalesOrder);
                }
            });

            goodsReceiptTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemGoodsReceipt');
                if(table.find('.item-row').length === 0) {
                    displayApprovalData(table, 'goods-receipts', 9, goodsReceiptTab, datatableGoodsReceipt);
                }
            });

            deliveryOrderTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemDeliveryOrder');
                if(table.find('.item-row').length === 0) {
                    displayApprovalData(table, 'delivery-orders', 9, deliveryOrderTab, datatableDeliveryOrder);
                }
            });

            productTransferTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemProductTransfer');
                if(table.find('.item-row').length === 0) {
                    displayApprovalData(table, 'product-transfers', 8, productTransferTab, datatableProductTransfer);
                }
            });

            salesReturnTab.on('click', function (e) {
                e.preventDefault();

                let table = $('#itemSalesReturn');
                if(table.find('.item-row').length === 0) {
                    displayApprovalData(table, 'sales-returns', 9, salesReturnTab, datatableSalesReturn);
                }
            });

            function displayApprovalData(table, subject, colspan, tabItem, datatable) {
                $.ajax({
                    url: '{{ route('approvals.index-ajax') }}',
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
                                    case 'sales-orders':
                                        newRow = salesOrderItemRow(rowNumber, item);
                                        break;
                                    case 'goods-receipts':
                                        newRow = goodsReceiptItemRow(rowNumber, item);
                                        break;
                                    case 'delivery-orders':
                                        newRow = deliveryOrderItemRow(rowNumber, item);
                                        break;
                                    case 'product-transfers':
                                        newRow = productTransferItemRow(rowNumber, item);
                                        break;
                                    case 'sales-returns':
                                        newRow = salesReturnItemRow(rowNumber, item);
                                        break;
                                    default:
                                        return;
                                }

                                table.append(newRow);
                                rowNumber++;
                            });

                            let hasBadge = tabItem.find('.notification-badge');

                            if(hasBadge.length) {
                                hasBadge.text(approvals.length);
                            } else {
                                tabItem.append(approvalDataCountElement(approvals.length));
                            }

                            if (datatable) {
                                datatable.clear();

                                if (approvals.length > 0) {
                                    datatable.rows.add(table.find('tr'));
                                }

                                datatable.draw(false);
                            }
                        }
                    },
                })
            }

            function salesOrderItemRow(rowNumber, item) {
                let baseUrl = `{{ route('sales-orders.detail', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.subject_id);
                let urlEdit = `{{ route('approvals.show', '') }}` + '/' + item.id;

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

            function goodsReceiptItemRow(rowNumber, item) {
                let baseUrl = `{{ route('goods-receipts.detail', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.subject_id);
                let urlEdit = `{{ route('approvals.show', '') }}` + '/' + item.id;

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

            function deliveryOrderItemRow(rowNumber, item) {
                let baseUrl = `{{ route('delivery-orders.detail', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.subject_id);
                let urlEdit = `{{ route('approvals.show', '') }}` + '/' + item.id;

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

            function productTransferItemRow(rowNumber, item) {
                let baseUrl = `{{ route('product-transfers.detail', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.subject_id);
                let urlEdit = `{{ route('approvals.show', '') }}` + '/' + item.id;

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

            function salesReturnItemRow(rowNumber, item) {
                let baseUrl = `{{ route('sales-returns.show', 'id') }}`;
                let urlDetail = baseUrl.replace('id', item.subject_id);
                let urlEdit = `{{ route('approvals.show', '') }}` + '/' + item.id;

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

            function emptyItemRow(colspan) {
                return `
                    <tr>
                        <td colspan="${colspan}" class="text-center text-bold text-dark h4 py-2">No Data Available</td>
                    </tr>
                `;
            }

            function approvalDataCountElement(total) {
                return `
                    <span class="notification-badge">${total}</span>
                `;
            }

            function formatDate(dateStr, format = 'd-M-y') {
                const date = new Date(dateStr);

                if (format === 'Ymd') {
                    return date.toISOString().split('T')[0].replace(/-/g, '');
                } else if (format === 'd-M-y') {
                    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: '2-digit' });
                }

                return dateStr;
            }

            function getApprovalTypeLabel(type) {
                const labels = {
                    'EDIT': 'Edit',
                    'CANCEL': 'Cancel',
                    'APPROVAL_LIMIT': 'Approval Limit'
                };

                return labels[type];
            }
        });
    </script>
@endpush
