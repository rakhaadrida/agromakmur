<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\DeliveryOrder;
use App\Models\GoodsReceipt;
use App\Models\ProductTransfer;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\DeliveryOrderService;
use App\Utilities\Services\GoodsReceiptService;
use App\Utilities\Services\ProductTransferService;
use App\Utilities\Services\SalesOrderService;
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
            ->orderBy('approvals.date')
            ->get();

        $data = [
            'approvals' => $approvals
        ];

        return view('pages.admin.approval.index', $data);
    }

    public function indexAjax(Request $request) {
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
            default:
                return response()->json([
                    'message' => 'Invalid subject type'
                ], 400);
        }

        $baseQuery = ApprovalService::getBaseQueryIndex($subject);

        $approvals = $baseQuery
            ->with(['subject'])
            ->orderBy('approvals.date');

        if($filter->subject === 'goods-receipts') {
            $approvals = $approvals->with(['subject.supplier']);
        } else if($filter->subject === 'delivery-orders') {
            $approvals = $approvals->with(['subject.customer']);
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

        return view('pages.admin.approval.detail', $data);
    }

    public function update(Request $request, $id) {
        try {
            DB::beginTransaction();

            $approval = Approval::query()->findOrFail($id);

            $approval->update([
                'status' => Constant::APPROVAL_STATUS_APPROVED,
                'updated_by' => Auth::user()->id,
            ]);

            $approval->activeChild()->update([
                'status' => Constant::APPROVAL_STATUS_APPROVED,
                'updated_by' => Auth::user()->id,
            ]);

            switch ($approval->subject_type) {
                case SalesOrder::class:
                    if($approval->type == Constant::APPROVAL_TYPE_EDIT) {
                        $approval = $approval->activeChild;
                    }

                    SalesOrderService::handleApprovalData($approval->subject_id, $approval);
                    break;
                case GoodsReceipt::class:
                    if($approval->type == Constant::APPROVAL_TYPE_EDIT) {
                       $approval = $approval->activeChild;
                    }

                    GoodsReceiptService::handleApprovalData($approval->subject_id, $approval);
                    break;
                case DeliveryOrder::class:
                    if($approval->type == Constant::APPROVAL_TYPE_EDIT) {
                        $approval = $approval->activeChild;
                    }

                    DeliveryOrderService::handleApprovalData($approval->subject_id, $approval);
                    break;
                case ProductTransfer::class:
                    ProductTransferService::handleApprovalData($approval->subject_id);
                    break;
                default:
                    abort(404, 'Invalid subject type');
            }

            DB::commit();

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

            DB::commit();

            return redirect()->route('approvals.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }
}
