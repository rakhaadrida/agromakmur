<?php

use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;

function isUserSuperAdmin(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_SUPER_ADMIN]);
}

function isUserAdmin(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_SUPER_ADMIN, Constant::USER_ROLE_ADMIN]);
}

function isUserAdminOnly(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_ADMIN]);
}

function isUserFinance(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_FINANCE]);
}

function isUserWarehouse(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_WAREHOUSE]);
}

function getApprovalRoute() : array
{
    return [
        'approvals.*',
        'approval-histories',
    ];
}

function getMasterRoute() : array
{
    return [
        'users.*',
        'marketings.*',
        'suppliers.*',
        'customers.*',
        'warehouses.*',
        'prices.*',
        'categories.*',
        'subcategories.*',
        'units.*',
        'products.*',
        'deleted-users',
        'deleted-marketings',
        'deleted-suppliers',
        'deleted-customers',
        'deleted-warehouses',
        'deleted-prices',
        'deleted-categories',
        'deleted-subcategories',
        'deleted-units',
        'deleted-products',
    ];
}

function getPurchaseRoute() : array
{
    return [
        'plan-orders.*',
        'goods-receipts.*',
        'product-transfers.*',
        'print-plan-orders',
        'print-goods-receipts',
        'print-product-transfers',
        'edit-goods-receipts',
    ];
}

function getSalesRoute() : array
{
    return [
        'sales-orders.*',
        'delivery-orders.*',
        'print-sales-orders',
        'print-delivery-orders',
        'edit-sales-orders',
        'edit-delivery-orders',
    ];
}

function getReturnRoute() : array
{
    return [
        'returns.*',
        'sales-returns.*',
        'purchase-returns.*',
    ];
}

function getProductReportRoute() : array
{
    return [
        'report.product-histories.*',
        'report.low-stocks.*',
        'report.stock-cards.*',
        'report.price-lists.*',
        'report.incoming-items.*',
        'report.outgoing-items.*',
        'report.stock-recap.*',
        'report.value-recap.*',
        'report.marketing-recap.*',
    ];
}

function getTransactionReportRoute() : array
{
    return [
        'report.sales-recap.*',
        'report.purchase-recap.*',
    ];
}

function getReceivableRoute() : array
{
    return [
        'account-receivables.*',
        'check-invoices',
    ];
}

function getUserRoleLabel($role): string
{
    return Constant::USER_ROLE_LABELS[$role];
}

function getWarehouseTypeLabel($type): string
{
    return Constant::WAREHOUSE_TYPE_LABELS[$type];
}

function getPriceTypeLabel($type): string
{
    return Constant::PRICE_TYPE_LABELS[$type];
}

function getSalesOrderTypeLabel($type): string
{
    return Constant::SALES_ORDER_TYPE_LABELS[$type];
}

function getGoodsReceiptStatusLabel($status): string
{
    return Constant::GOODS_RECEIPT_STATUS_LABELS[$status];
}

function getProductTransferStatusLabel($status): string
{
    return Constant::PRODUCT_TRANSFER_STATUS_LABELS[$status];
}

function getSalesOrderStatusLabel($status): string
{
    return Constant::SALES_ORDER_STATUS_LABELS[$status];
}

function getDeliveryOrderStatusLabel($status): string
{
    return Constant::DELIVERY_ORDER_STATUS_LABELS[$status];
}

function getSalesReturnStatusLabel($status): string
{
    return Constant::SALES_RETURN_STATUS_LABELS[$status];
}

function getSalesReturnDeliveryStatusLabel($deliveryStatus): string
{
    return Constant::SALES_RETURN_DELIVERY_STATUS_LABELS[$deliveryStatus];
}

function getPurchaseReturnStatusLabel($status): string
{
    return Constant::PURCHASE_RETURN_STATUS_LABELS[$status];
}

function getPurchaseReturnReceiptStatusLabel($receiptStatus): string
{
    return Constant::PURCHASE_RETURN_RECEIPT_STATUS_LABELS[$receiptStatus];
}

function getAccountPayableStatusLabel($status): string
{
    return Constant::ACCOUNT_PAYABLE_STATUS_LABELS[$status];
}

function getAccountReceivableStatusLabel($status): string
{
    return Constant::ACCOUNT_RECEIVABLE_STATUS_LABELS[$status];
}

function getApprovalSubjectTypeLabel($subjectType): string
{
    return Constant::APPROVAL_SUBJECT_TYPE_LABELS[$subjectType];
}

function getApprovalStatusLabel($status): string
{
    return Constant::APPROVAL_STATUS_LABELS[$status];
}

function getApprovalTypeLabel($type): string
{
    return Constant::APPROVAL_TYPE_LABELS[$type];
}

function getProductStockLogTypeLabel($type): string
{
    return Constant::PRODUCT_STOCK_LOG_TYPE_LABELS[$type];
}

function getProductStockLogTypeRoute($type): string
{
    return Constant::PRODUCT_STOCK_LOG_TYPE_ROUTE[$type];
}

function isWaitingApproval($status): bool
{
    return in_array($status, [
        Constant::PRODUCT_TRANSFER_STATUS_WAITING_APPROVAL,
        Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL,
        Constant::SALES_ORDER_STATUS_WAITING_APPROVAL,
        Constant::SALES_RETURN_STATUS_WAITING_APPROVAL,
        Constant::PURCHASE_RETURN_STATUS_WAITING_APPROVAL
    ]);
}

function isCancelled($status): bool
{
    return in_array($status, [
        Constant::PRODUCT_TRANSFER_STATUS_CANCELLED,
        Constant::GOODS_RECEIPT_STATUS_CANCELLED,
        Constant::SALES_ORDER_STATUS_CANCELLED,
        Constant::SALES_RETURN_STATUS_CANCELLED,
        Constant::PURCHASE_RETURN_STATUS_CANCELLED
    ]);
}

function isApprovalSalesReceiptTransaction($subjectType): bool
{
    return in_array($subjectType, [Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER, Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT]);
}

function isApprovalSubjectTypeSalesOrder($subjectType): bool
{
    return $subjectType == Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER;
}

function isApprovalSubjectTypeGoodsReceipt($subjectType): bool
{
    return $subjectType == Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT;
}

function isApprovalSubjectTypeDeliveryOrder($subjectType): bool
{
    return $subjectType == Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER;
}

function isApprovalSubjectTypeProductTransfer($subjectType): bool
{
    return $subjectType == Constant::APPROVAL_SUBJECT_TYPE_PRODUCT_TRANSFER;
}

function isApprovalSubjectTypeSalesReturn($subjectType): bool
{
    return $subjectType == Constant::APPROVAL_SUBJECT_TYPE_SALES_RETURN;
}

function isApprovalSubjectTypePurchaseReturn($subjectType): bool
{
    return $subjectType == Constant::APPROVAL_SUBJECT_TYPE_PURCHASE_RETURN;
}

function isApprovalTypeEdit($type): bool
{
    return $type == Constant::APPROVAL_TYPE_EDIT;
}

function isApprovalTypeApprovalLimit($type): bool
{
    return $type == Constant::APPROVAL_TYPE_APPROVAL_LIMIT;
}

function isApprovalTypeCancel($type): bool
{
    return $type == Constant::APPROVAL_TYPE_CANCEL;
}

function isDifferenceApprovalItem($childItem, $parentItem): bool
{
    return $childItem != $parentItem;
}

function isSalesReturnActive($status): bool
{
    return $status == Constant::SALES_RETURN_DELIVERY_STATUS_ACTIVE;
}

function isSalesReturnOngoing($status): bool
{
    return $status == Constant::SALES_RETURN_DELIVERY_STATUS_ONGOING;
}

function isSalesReturnCompleted($status): bool
{
    return $status == Constant::SALES_RETURN_DELIVERY_STATUS_COMPLETED;
}

function isPurchaseReturnActive($status): bool
{
    return $status == Constant::PURCHASE_RETURN_RECEIPT_STATUS_ACTIVE;
}

function isPurchaseReturnOngoing($status): bool
{
    return $status == Constant::PURCHASE_RETURN_RECEIPT_STATUS_ONGOING;
}

function isPurchaseReturnCompleted($status): bool
{
    return $status == Constant::PURCHASE_RETURN_RECEIPT_STATUS_COMPLETED;
}

function isAccountPayableUnpaid($status): bool
{
    return $status == Constant::ACCOUNT_PAYABLE_STATUS_UNPAID;
}

function isAccountPayableOngoing($status): bool
{
    return $status == Constant::ACCOUNT_PAYABLE_STATUS_ONGOING;
}

function isAccountPayablePaid($status): bool
{
    return $status == Constant::ACCOUNT_PAYABLE_STATUS_PAID;
}

function isAccountReceivableUnpaid($status): bool
{
    return $status == Constant::ACCOUNT_RECEIVABLE_STATUS_UNPAID;
}

function isAccountReceivableOngoing($status): bool
{
    return $status == Constant::ACCOUNT_RECEIVABLE_STATUS_ONGOING;
}

function isAccountReceivablePaid($status): bool
{
    return $status == Constant::ACCOUNT_RECEIVABLE_STATUS_PAID;
}

function isSupplierLog($type): bool
{
    return in_array($type, [
        Constant::PRODUCT_STOCK_LOG_TYPE_GOODS_RECEIPT,
        Constant::PRODUCT_STOCK_LOG_TYPE_PURCHASE_RETURN,
    ]);
}

function isCustomerLog($type): bool
{
    return in_array($type, [
        Constant::PRODUCT_STOCK_LOG_TYPE_SALES_ORDER,
        Constant::PRODUCT_STOCK_LOG_TYPE_SALES_RETURN,
    ]);
}

function isReturnLog($type): bool
{
    return in_array($type, [
        Constant::PRODUCT_STOCK_LOG_TYPE_SALES_RETURN,
        Constant::PRODUCT_STOCK_LOG_TYPE_PURCHASE_RETURN,
    ]);
}

function isTransactionLog($type): bool
{
    return in_array($type, [
        Constant::PRODUCT_STOCK_LOG_TYPE_GOODS_RECEIPT,
        Constant::PRODUCT_STOCK_LOG_TYPE_PRODUCT_TRANSFER,
        Constant::PRODUCT_STOCK_LOG_TYPE_SALES_ORDER,
    ]);
}

function isActiveData($item): string
{
    return empty($item->deleted_at) ? 'Active' : 'Inactive';
}

function isSameWarehouse($warehouseId, $itemId): string
{
    return $warehouseId == $itemId;
}

function isNotEmptyMarketingRecap($items): bool
{
    return !empty($items);
}

function isSubjectProduct($subject): bool
{
    return $subject == 'products' || $subject == 'product';
}

function isSubjectCustomer($subject): bool
{
    return $subject == 'customers' || $subject == 'customer';
}

function isSubjectSupplier($subject): bool
{
    return $subject == 'suppliers' || $subject == 'supplier';
}

function getDueDate($date, $tempo, $format): string
{
    $dueDate = \Carbon\Carbon::parse($date)->add($tempo, 'days');

    return formatDate($dueDate, $format);
}

function getInvoiceAge($date, $tempo): int
{
    $now = \Carbon\Carbon::now();
    $invoiceDate = \Carbon\Carbon::parse($date);

    $diffInDays = $now->diffInDays($invoiceDate);

    return $diffInDays;
}

function getRealQuantity($quantity, $actualQuantity)
{
    return $actualQuantity / $quantity;
}

function getTotalArray($array)
{
    return formatQuantity(array_sum($array));
}

function getTotalArrayExport($array)
{
    return array_sum($array);
}

function getGrandTotal($object, $column)
{
    return formatPrice($object->sum($column));
}

function getTotalQuantity($object)
{
    return formatPrice($object->sum('total_quantity'));
}

function getActualPrice($quantity, $actualQuantity, $price)
{
    $realQuantity = $actualQuantity / $quantity;

    return $price / $realQuantity;
}

function formatDate($date, $format)
{
    return \Carbon\Carbon::parse($date)->format($format);
}

function formatCurrency($amount)
{
    return number_format($amount, 0, '', ',');
}

function formatPrice($amount)
{
    return number_format($amount, 0, ',', '.');
}

function formatQuantity($amount)
{
    return number_format($amount, 0, ',', '.');
}
