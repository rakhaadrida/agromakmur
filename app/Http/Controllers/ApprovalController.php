<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\DeliveryOrder;
use App\Models\GoodsReceipt;
use App\Models\ProductTransfer;
use App\Models\PurchaseReturn;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Models\Warehouse;
use App\Notifications\RejectApprovalNotification;
use App\Notifications\RequestApprovedNotification;
use App\Utilities\Constant;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\DeliveryOrderService;
use App\Utilities\Services\GoodsReceiptService;
use App\Utilities\Services\NotificationService;
use App\Utilities\Services\ProductTransferService;
use App\Utilities\Services\PurchaseReturnService;
use App\Utilities\Services\SalesOrderService;
use App\Utilities\Services\SalesReturnService;
use App\Utilities\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    public function index(Request $request) {
        $baseQuery = ApprovalService::getBaseQueryIndex(SalesOrder::class);

        $approvals = $baseQuery
            ->where('approvals.status', Constant::APPROVAL_STATUS_PENDING)
            ->orderByDesc('approvals.date')
            ->get();

        $baseQueryCountApprovals = ApprovalService::getBaseQueryCount();
        $countApprovals = $baseQueryCountApprovals->get();

        $mapCountApprovalBySubject = [];
        foreach($countApprovals as $countApproval) {
            switch ($countApproval->subject_type) {
                case SalesOrder::class:
                    $mapCountApprovalBySubject[Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER] = $countApproval->total_approvals;
                    break;
                case GoodsReceipt::class:
                    $mapCountApprovalBySubject[Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT] = $countApproval->total_approvals;
                    break;
                case DeliveryOrder::class:
                    $mapCountApprovalBySubject[Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER] = $countApproval->total_approvals;
                    break;
                case ProductTransfer::class:
                    $mapCountApprovalBySubject[Constant::APPROVAL_SUBJECT_TYPE_PRODUCT_TRANSFER] = $countApproval->total_approvals;
                    break;
                case SalesReturn::class:
                    $mapCountApprovalBySubject[Constant::APPROVAL_SUBJECT_TYPE_SALES_RETURN] = $countApproval->total_approvals;
                    break;
                case PurchaseReturn::class:
                    $mapCountApprovalBySubject[Constant::APPROVAL_SUBJECT_TYPE_PURCHASE_RETURN] = $countApproval->total_approvals;
                    break;
                default:
                    abort(404, 'Invalid subject type');
            }
        }

        $data = [
            'approvals' => $approvals,
            'mapCountApprovalBySubject' => $mapCountApprovalBySubject,
        ];

        return view('pages.admin.approval.index', $data);
    }

    public function indexAjax(Request $request) {
        $filter = (object) $request->all();
        $subject = $filter->subject ?? null;

        switch ($subject) {
            case 'sales-orders':
                $subject = SalesOrder::class;
                break;
            case 'goods-receipts':
                $subject = GoodsReceipt::class;
                break;
            case 'delivery-orders':
                $subject = DeliveryOrder::class;
                break;
            case 'product-transfers':
                $subject = ProductTransfer::class;
                break;
            case 'sales-returns':
                $subject = SalesReturn::class;
                break;
            case 'purchase-returns':
                $subject = PurchaseReturn::class;
                break;
            default:
                return response()->json([
                    'message' => 'Invalid subject type'
                ], 400);
        }

        $baseQuery = ApprovalService::getBaseQueryIndex($subject);

        $approvals = $baseQuery
            ->with(['subject'])
            ->where('approvals.status', Constant::APPROVAL_STATUS_PENDING)
            ->orderByDesc('approvals.date');

        if($filter->subject == 'goods-receipts') {
            $approvals = $approvals->with(['subject.supplier', 'subject.branch']);
        } else if(in_array($filter->subject, ['sales-orders', 'delivery-orders'])) {
            $approvals = $approvals->with(['subject.customer', 'subject.branch']);
        } else if($filter->subject == 'purchase-returns') {
            $approvals = $approvals->with(['subject.supplier', 'subject.goodsReceipt.branch']);
        } else if($filter->subject == 'sales-returns') {
            $approvals = $approvals->with(['subject.customer', 'subject.salesOrder.branch']);
        }

        $approvals = $approvals->get();

        return response()->json([
            'data' => $approvals,
        ]);
    }

    public function show($id) {
        $approval = Approval::query()->findOrFail($id);
        $childData = $approval->activeChild;
        $productWarehouses = [];
        $childProductWarehouses = [];

        $approvalItems = $approval->approvalItems()->orderBy('product_id')->get();

        switch ($approval->subject_type) {
            case SalesOrder::class:
                $approval->client_label = 'Customer';
                $approval->client_name = $approval->subject->customer->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER;

                foreach($approval->approvalItems as $approvalItem) {
                    $productWarehouses[$approvalItem->product_id][$approvalItem->warehouse_id] = $approvalItem->quantity;
                }

                $approval->approvalItems = SalesOrderService::mapSalesOrderItemDetail($approvalItems);

                break;
            case GoodsReceipt::class:
                $approval->client_label = 'Supplier';
                $approval->client_name = $approval->subject->supplier->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT;
                $approval->approvalItems = $approvalItems;
                break;
            case DeliveryOrder::class:
                $approval->client_label = 'Customer';
                $approval->client_name = $approval->subject->customer->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER;
                $approval->approvalItems = $approvalItems;
                break;
            case ProductTransfer::class:
                $approval->client_label = '';
                $approval->client_name = '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_PRODUCT_TRANSFER;
                $approval->approvalItems = $approval->subject->productTransferItems;
                break;
            case SalesReturn::class:
                $approval->client_label = 'Customer';
                $approval->client_name = $approval->subject->customer->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_SALES_RETURN;
                $approval->approvalItems = $approvalItems;

                $productIds = $approvalItems->pluck('product_id')->toArray();
                $orderQuantities = SalesOrderService::getSalesOrderQuantityBySalesOrderProductIds($approval->subject->sales_order_id, $productIds);

                $mapOrderQuantityByProductId = [];
                foreach($orderQuantities as $orderQuantity) {
                    $mapOrderQuantityByProductId[$orderQuantity->product_id] = $orderQuantity->quantity;
                }

                foreach($approval->approvalItems as $approvalItem) {
                    $approvalItem->order_quantity = $mapOrderQuantityByProductId[$approvalItem->product_id] ?? 0;

                    $remainingQuantity = $approvalItem->quantity - $approvalItem->delivered_quantity - $approvalItem->cut_bill_quantity;
                    $approvalItem->remaining_quantity = $remainingQuantity;
                }
                break;
            case PurchaseReturn::class:
                $approval->client_label = 'Supplier';
                $approval->client_name = $approval->subject->supplier->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_PURCHASE_RETURN;
                $approval->approvalItems = $approvalItems;

                $productIds = $approvalItems->pluck('product_id')->toArray();
                $receiptQuantities = GoodsReceiptService::getGoodsReceiptQuantityByGoodsReceiptProductIds($approval->subject->goods_receipt_id, $productIds);

                $mapReceiptQuantityByProductId = [];
                foreach($receiptQuantities as $receiptQuantity) {
                    $mapReceiptQuantityByProductId[$receiptQuantity->product_id] = $receiptQuantity->quantity;
                }

                foreach($approval->approvalItems as $approvalItem) {
                    $approvalItem->receipt_quantity = $mapReceiptQuantityByProductId[$approvalItem->product_id] ?? 0;

                    $remainingQuantity = $approvalItem->quantity - $approvalItem->received_quantity - $approvalItem->cut_bill_quantity;
                    $approvalItem->remaining_quantity = $remainingQuantity;
                }
                break;
            default:
                abort(404, 'Invalid subject type');
        }

        $approvalItems = $approval->approvalItems;

        if($childData) {
            $childItems = $childData->approvalItems()->orderBy('product_id')->get();

            switch ($childData->subject_type) {
                case SalesOrder::class:
                    $childData->subject_label = Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER;

                    $revision = ApprovalService::getRevisionCountBySubject(SalesOrder::class, [$childData->subject_id]);
                    $childData->revision = $revision + 1;

                    foreach($childData->approvalItems as $approvalItem) {
                        $childProductWarehouses[$approvalItem->product_id][$approvalItem->warehouse_id] = $approvalItem->quantity;
                    }

                    $childData->approvalItems = SalesOrderService::mapSalesOrderItemDetail($childItems);

                    break;
                case GoodsReceipt::class:
                    $childData->subject_label = Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT;
                    $childData->approvalItems = $childItems;

                    $revision = ApprovalService::getRevisionCountBySubject(GoodsReceipt::class, [$childData->subject_id]);
                    $childData->revision = $revision + 1;

                    break;
                case DeliveryOrder::class:
                    $childData->subject_label = Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER;
                    $childData->approvalItems = $childItems;

                    $revision = ApprovalService::getRevisionCountBySubject(DeliveryOrder::class, [$childData->subject_id]);
                    $childData->revision = $revision + 1;

                    break;
                default:
                    abort(404, 'Invalid subject type');
            }
        }

        $warehouses = Warehouse::all();
        $totalWarehouses = $warehouses->count();

        $data = [
            'id' => $id,
            'approval' => $approval,
            'approvalItems' => $approvalItems,
            'childData' => $childData,
            'warehouses' => $warehouses,
            'totalWarehouses' => $totalWarehouses,
            'productWarehouses' => $productWarehouses ?? [],
            'childProductWarehouses' => $childProductWarehouses ?? [],
        ];

        return view('pages.admin.approval.detail', $data);
    }

    public function update(Request $request, $id) {
        try {
            DB::beginTransaction();

            $approval = Approval::query()->findOrFail($id);
            $parentApproval = $approval;

            $approval->update([
                'status' => Constant::APPROVAL_STATUS_APPROVED,
                'updated_by' => Auth::user()->id,
            ]);

            $approval->activeChild()->update([
                'status' => Constant::APPROVAL_STATUS_APPROVED,
                'updated_by' => Auth::user()->id,
            ]);

            $branchId = $approval->subject->branch_id ?? null;
            $subjectType = $approval->subject_type;
            switch ($approval->subject_type) {
                case SalesOrder::class:
                    if($approval->type == Constant::APPROVAL_TYPE_EDIT) {
                        $approval = $approval->activeChild;
                    }

                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER;
                    SalesOrderService::handleApprovalData($approval->subject_id, $approval);
                    break;
                case GoodsReceipt::class:
                    if($approval->type == Constant::APPROVAL_TYPE_EDIT) {
                       $approval = $approval->activeChild;
                    }

                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT;
                    GoodsReceiptService::handleApprovalData($approval->subject_id, $approval);
                    break;
                case DeliveryOrder::class:
                    if($approval->type == Constant::APPROVAL_TYPE_EDIT) {
                        $approval = $approval->activeChild;
                    }

                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER;
                    DeliveryOrderService::handleApprovalData($approval->subject_id, $approval);
                    break;
                case ProductTransfer::class:
                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_PRODUCT_TRANSFER;
                    ProductTransferService::handleApprovalData($approval->subject_id);
                    break;
                case SalesReturn::class:
                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_SALES_RETURN;
                    SalesReturnService::handleApprovalData($approval->subject_id);
                    $branchId = $approval->subject->salesOrder->branch_id;

                    break;
                case PurchaseReturn::class:
                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_PURCHASE_RETURN;
                    PurchaseReturnService::handleApprovalData($approval->subject_id);
                    $branchId = $approval->subject->goodsReceipt->branch_id;

                    break;
                default:
                    abort(404, 'Invalid subject type');
            }

            $subjectLabel = Constant::APPROVAL_SUBJECT_TYPE_LABELS[$subjectType] ?? 'Unknown Subject';
            NotificationService::markAsReadRequestNotification($id);

            DB::commit();

            $users = UserService::getAdminUsers($branchId);

            foreach($users as $user) {
                $user->notify(new RequestApprovedNotification($approval->subject->number, $subjectLabel, $parentApproval->id));
            }

            return redirect()->route('approvals.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('approvals.show', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy(Request $request, $id) {
        try {
            DB::beginTransaction();

            $approvals = Approval::query()
                ->where('id', $id)
                ->orWhere('parent_id', $id)
                ->whereNull('deleted_at')
                ->get();


            foreach($approvals as $approval) {
                $approval->update([
                    'status' => Constant::APPROVAL_STATUS_REJECTED,
                    'updated_by' => Auth::user()->id,
                ]);

                $approval->subject()->update([
                    'status' => Constant::SALES_ORDER_STATUS_ACTIVE
                ]);
            }

            NotificationService::markAsReadRequestNotification($id);

            DB::commit();

            $approval = Approval::query()->findOrFail($id);

            $branchId = $approval->subject->branch_id ?? null;
            $subjectType = $approval->subject_type;
            switch ($approval->subject_type) {
                case SalesOrder::class:
                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER;
                    break;
                case GoodsReceipt::class:
                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT;
                    break;
                case DeliveryOrder::class:
                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER;
                    break;
                case ProductTransfer::class:
                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_PRODUCT_TRANSFER;
                    break;
                case SalesReturn::class:
                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_SALES_RETURN;
                    $branchId = $approval->subject->salesOrder->branch_id;
                    break;
                case PurchaseReturn::class:
                    $subjectType = Constant::APPROVAL_SUBJECT_TYPE_PURCHASE_RETURN;
                    $branchId = $approval->subject->goodsReceipt->branch_id;
                    break;
                default:
                    abort(404, 'Invalid subject type');
            }

            $subjectLabel = Constant::APPROVAL_SUBJECT_TYPE_LABELS[$subjectType] ?? 'Unknown Subject';
            $users = UserService::getAdminUsers($branchId);

            foreach($users as $user) {
                $user->notify(new RejectApprovalNotification($approval->subject->number, $subjectLabel, $approval->id));
            }

            return redirect()->route('approvals.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexHistory(Request $request) {
        $baseQuery = ApprovalService::getBaseQueryIndex(SalesOrder::class);

        $approvals = $baseQuery
            ->where('approvals.status', '!=', Constant::APPROVAL_STATUS_PENDING)
            ->orderByDesc('approvals.date')
            ->get();

        $data = [
            'approvals' => $approvals
        ];

        return view('pages.admin.approval.index-history', $data);
    }

    public function indexHistoryAjax(Request $request) {
        $filter = (object) $request->all();
        $subject = $filter->subject ?? null;

        switch ($subject) {
            case 'goods-receipts':
                $subject = GoodsReceipt::class;
                break;
            case 'delivery-orders':
                $subject = DeliveryOrder::class;
                break;
            case 'product-transfers':
                $subject = ProductTransfer::class;
                break;
            case 'sales-returns':
                $subject = SalesReturn::class;
                break;
            case 'purchase-returns':
                $subject = PurchaseReturn::class;
                break;
            default:
                return response()->json([
                    'message' => 'Invalid subject type'
                ], 400);
        }

        $baseQuery = ApprovalService::getBaseQueryIndex($subject);

        $approvals = $baseQuery
            ->with(['subject'])
            ->where('approvals.status', '!=', Constant::APPROVAL_STATUS_PENDING)
            ->orderByDesc('approvals.date');

        if($filter->subject == 'goods-receipts') {
            $approvals = $approvals->with(['subject.supplier', 'subject.branch']);
        } else if(in_array($filter->subject, ['sales-orders', 'delivery-orders'])) {
            $approvals = $approvals->with(['subject.customer', 'subject.branch']);
        } else if($filter->subject == 'purchase-returns') {
            $approvals = $approvals->with(['subject.supplier', 'subject.goodsReceipt.branch']);
        } else if($filter->subject == 'sales-returns') {
            $approvals = $approvals->with(['subject.customer', 'subject.salesOrder.branch']);
        }

        $approvals = $approvals->get();

        return response()->json([
            'data' => $approvals,
        ]);
    }

    public function detail($id) {
        $approval = Approval::query()->findOrFail($id);
        $childData = $approval->activeChild;
        $productWarehouses = [];
        $childProductWarehouses = [];

        $approvalItems = $approval->approvalItems()->orderBy('product_id')->get();

        switch ($approval->subject_type) {
            case SalesOrder::class:
                $approval->client_label = 'Customer';
                $approval->client_name = $approval->subject->customer->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER;

                foreach($approval->approvalItems as $approvalItem) {
                    $productWarehouses[$approvalItem->product_id][$approvalItem->warehouse_id] = $approvalItem->quantity;
                }

                $approval->approvalItems = SalesOrderService::mapSalesOrderItemDetail($approvalItems);

                break;
            case GoodsReceipt::class:
                $approval->client_label = 'Supplier';
                $approval->client_name = $approval->subject->supplier->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT;
                $approval->approvalItems = $approvalItems;
                break;
            case DeliveryOrder::class:
                $approval->client_label = 'Customer';
                $approval->client_name = $approval->subject->customer->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER;
                $approval->approvalItems = $approvalItems;
                break;
            case ProductTransfer::class:
                $approval->client_label = '';
                $approval->client_name = '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_PRODUCT_TRANSFER;
                $approval->approvalItems = $approval->subject->productTransferItems;
                break;
            case SalesReturn::class:
                $approval->client_label = 'Customer';
                $approval->client_name = $approval->subject->customer->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_SALES_RETURN;
                $approval->approvalItems = $approvalItems;

                $productIds = $approvalItems->pluck('product_id')->toArray();
                $orderQuantities = SalesOrderService::getSalesOrderQuantityBySalesOrderProductIds($approval->subject->sales_order_id, $productIds);

                $mapOrderQuantityByProductId = [];
                foreach($orderQuantities as $orderQuantity) {
                    $mapOrderQuantityByProductId[$orderQuantity->product_id] = $orderQuantity->quantity;
                }

                foreach($approval->approvalItems as $approvalItem) {
                    $approvalItem->order_quantity = $mapOrderQuantityByProductId[$approvalItem->product_id] ?? 0;

                    $remainingQuantity = $approvalItem->quantity - $approvalItem->delivered_quantity - $approvalItem->cut_bill_quantity;
                    $approvalItem->remaining_quantity = $remainingQuantity;
                }
                break;
            case PurchaseReturn::class:
                $approval->client_label = 'Supplier';
                $approval->client_name = $approval->subject->supplier->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_PURCHASE_RETURN;
                $approval->approvalItems = $approvalItems;

                $productIds = $approvalItems->pluck('product_id')->toArray();
                $receiptQuantities = GoodsReceiptService::getGoodsReceiptQuantityByGoodsReceiptProductIds($approval->subject->goods_receipt_id, $productIds);

                $mapReceiptQuantityByProductId = [];
                foreach($receiptQuantities as $receiptQuantity) {
                    $mapReceiptQuantityByProductId[$receiptQuantity->product_id] = $receiptQuantity->quantity;
                }

                foreach($approval->approvalItems as $approvalItem) {
                    $approvalItem->receipt_quantity = $mapReceiptQuantityByProductId[$approvalItem->product_id] ?? 0;

                    $remainingQuantity = $approvalItem->quantity - $approvalItem->received_quantity - $approvalItem->cut_bill_quantity;
                    $approvalItem->remaining_quantity = $remainingQuantity;
                }
                break;
            default:
                abort(404, 'Invalid subject type');
        }

        $approvalItems = $approval->approvalItems;

        if($childData) {
            $childItems = $childData->approvalItems()->orderBy('product_id')->get();

            switch ($childData->subject_type) {
                case SalesOrder::class:
                    $childData->subject_label = Constant::APPROVAL_SUBJECT_TYPE_SALES_ORDER;

                    foreach($childData->approvalItems as $approvalItem) {
                        $childProductWarehouses[$approvalItem->product_id][$approvalItem->warehouse_id] = $approvalItem->quantity;
                    }

                    $childData->approvalItems = SalesOrderService::mapSalesOrderItemDetail($childItems);

                    break;
                case GoodsReceipt::class:
                    $childData->subject_label = Constant::APPROVAL_SUBJECT_TYPE_GOODS_RECEIPT;
                    $childData->approvalItems = $childItems;
                    break;
                case DeliveryOrder::class:
                    $childData->subject_label = Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER;
                    $childData->approvalItems = $childItems;
                    break;
                default:
                    abort(404, 'Invalid subject type');
            }
        }

        $warehouses = Warehouse::all();
        $totalWarehouses = $warehouses->count();

        $data = [
            'id' => $id,
            'approval' => $approval,
            'approvalItems' => $approvalItems,
            'childData' => $childData,
            'warehouses' => $warehouses,
            'totalWarehouses' => $totalWarehouses,
            'productWarehouses' => $productWarehouses ?? [],
            'childProductWarehouses' => $childProductWarehouses ?? [],
        ];

        return view('pages.admin.approval.detail-history', $data);
    }
}
