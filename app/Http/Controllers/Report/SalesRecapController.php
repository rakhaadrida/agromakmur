<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrderItem;
use App\Utilities\Services\SalesRecapService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesRecapController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $salesItems = SalesRecapService::getBaseQueryProductIndex($startDate, $finalDate);

        $reportDate = Carbon::parse()->isoFormat('dddd, D MMMM Y, HH:mm:ss');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'salesItems' => $salesItems,
            'reportDate' => $reportDate,
        ];

        return view('pages.admin.report.sales-recap.index', $data);
    }

    public function indexAjax(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $subject = $filter->subject ?? null;

        switch ($subject) {
            case 'products':
                $salesItems = SalesRecapService::getBaseQueryProductIndex($startDate, $finalDate);
                break;
            case 'customers':
                $salesItems = SalesRecapService::getBaseQueryCustomerIndex($startDate, $finalDate);
                break;
            default:
                return response()->json([
                    'message' => 'Invalid subject type'
                ], 400);
        }

        return response()->json([
            'data' => $salesItems ?? [],
        ]);
    }

    public function show(Request $request, $id) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->startOfMonth()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $subject = $filter->subject ?? null;

        $customers = Customer::all();
        $products = Product::all();

        $baseQuery = SalesOrderItem::query()
            ->select(
                'sales_orders.id AS order_id',
                'sales_orders.date AS order_date',
                'sales_orders.number AS order_number',
                'customers.id AS customer_id',
                'customers.name AS customer_name',
                'units.name AS unit_name',
                'sales_order_items.actual_quantity AS quantity',
                'sales_order_items.price AS price',
                'sales_order_items.total AS total',
                'sales_order_items.discount_amount AS discount_amount',
                'sales_order_items.final_amount AS final_amount',
            )
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
            ->join('customers', 'customers.id', '=', 'sales_orders.customer_id')
            ->join('products', 'products.id', '=', 'sales_order_items.product_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->where('products.id', $id)
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->whereNull('goods_receipt_items.deleted_at')
            ->whereNull('goods_receipts.deleted_at');

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

        return view('pages.admin.approval.detail', $data);
    }
}
