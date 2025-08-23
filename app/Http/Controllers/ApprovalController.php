<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsReceiptCancelRequest;
use App\Http\Requests\GoodsReceiptCreateRequest;
use App\Http\Requests\GoodsReceiptUpdateRequest;
use App\Models\Approval;
use App\Models\DeliveryOrder;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\ProductTransfer;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\AccountPayableService;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\GoodsReceiptService;
use App\Utilities\Services\ProductService;
use App\Utilities\Services\SalesOrderService;
use Carbon\Carbon;
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
                break;
            case DeliveryOrder::class:
                $approval->client_label = 'Customer';
                $approval->client_name = $approval->subject->customer->name ?? '';
                $approval->subject_label = Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER;
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
        $childItems = $childData->approvalItems()->orderBy('product_id')->get();

        if($childData) {
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
                    break;
                case DeliveryOrder::class:
                    $childData->subject_label = Constant::APPROVAL_SUBJECT_TYPE_DELIVERY_ORDER;
                    break;
                case ProductTransfer::class:
                    $childData->subject_label = Constant::APPROVAL_SUBJECT_TYPE_PRODUCT_TRANSFER;
                    $childData->approvalItems = $approval->subject->productTransferItems;
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

    public function create() {
        $date = Carbon::now()->format('d-m-Y');
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $products = Product::all();
        $rows = range(1, 5);
        $rowNumbers = count($rows);

        $data = [
            'date' => $date,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'products' => $products,
            'rows' => $rows,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.admin.goods-receipt.create', $data);
    }

    public function store(GoodsReceiptCreateRequest $request) {
        try {
            DB::beginTransaction();

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $request->merge([
                'date' => $date,
                'tempo' => $request->get('tempo') || 0,
                'subtotal' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
                'status' => Constant::GOODS_RECEIPT_STATUS_ACTIVE,
                'user_id' => Auth::user()->id,
            ]);

            $goodsReceipt = GoodsReceipt::create($request->all());

            $subtotal = 0;
            $productIds = $request->get('product_id', []);
            foreach ($productIds as $index => $productId) {
                if(!empty($productId)) {
                    $unitId = $request->get('unit_id')[$index];
                    $quantity = $request->get('quantity')[$index];
                    $realQuantity = $request->get('real_quantity')[$index];
                    $price = $request->get('price')[$index];
                    $wages = $request->get('wages')[$index];
                    $shippingCost = $request->get('shipping_cost')[$index];

                    $actualQuantity = $quantity * $realQuantity;
                    $totalExpenses = $wages + $shippingCost;
                    $total = ($quantity * $price) + $totalExpenses;
                    $subtotal += $total;

                    $goodsReceipt->goodsReceiptItems()->create([
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'price' => $price,
                        'wages' => $wages,
                        'shipping_cost' => $shippingCost,
                        'total' => $total
                    ]);

                    $productStock = ProductService::getProductStockQuery(
                        $productId,
                        $goodsReceipt->warehouse_id
                    );

                    ProductService::updateProductStockIncrement(
                        $productId,
                        $productStock,
                        $actualQuantity,
                        $goodsReceipt->warehouse_id
                    );
                }
            }

            $taxAmount = $subtotal * (10 / 100);
            $grandTotal = $subtotal + $taxAmount;

            $goodsReceipt->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal
            ]);

            AccountPayableService::createData($goodsReceipt);

            $parameters = [];
            $route = 'goods-receipts.create';

            if($request->get('is_print')) {
                $route = 'goods-receipts.print';
                $parameters = ['id' => $goodsReceipt->id];
            }

            DB::commit();

            return redirect()->route($route, $parameters);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function indexEdit(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? null;
        $finalDate = $filter->final_date ?? null;
        $number = $filter->number ?? null;
        $supplierId = $filter->supplier_id ?? null;

        if(!$number && !$supplierId && !$startDate && !$finalDate) {
            $startDate = Carbon::now()->format('d-m-Y');
            $finalDate = Carbon::now()->format('d-m-Y');
        }

        $suppliers = Supplier::all();
        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        if($startDate) {
            $baseQuery = $baseQuery->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay());
        }

        if($finalDate) {
            $baseQuery = $baseQuery->where('goods_receipts.date', '<=', Carbon::parse($finalDate)->endOfDay());
        }

        if($number) {
            $baseQuery = $baseQuery->where('goods_receipts.number', $number);
        }

        if($supplierId) {
            $baseQuery = $baseQuery->where('goods_receipts.supplier_id', $supplierId);
        }

        $goodsReceipts = $baseQuery
            ->orderByDesc('goods_receipts.date')
            ->orderByDesc('goods_receipts.id')
            ->get();

        $goodsReceipts = GoodsReceiptService::mapGoodsReceiptIndex($goodsReceipts);

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'number' => $number,
            'supplierId' => $supplierId,
            'suppliers' => $suppliers,
            'goodsReceipts' => $goodsReceipts,
        ];

        return view('pages.admin.goods-receipt.index-edit', $data);
    }

    public function edit($id) {
        $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
        $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;

        if(isWaitingApproval($goodsReceipt->status) && isApprovalTypeEdit($goodsReceipt->pendingApproval->type)) {
            $goodsReceipt = GoodsReceiptService::mapGoodsReceiptApproval($goodsReceipt);
            $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;
        }

        $products = Product::all();
        $rowNumbers = count($goodsReceiptItems);

        $productIds = $goodsReceiptItems->pluck('product_id')->toArray();
        $productConversions = ProductService::findProductConversions($productIds);

        foreach($goodsReceiptItems as $goodsReceiptItem) {
            $units[$goodsReceiptItem->product_id][] = [
                'id' => $goodsReceiptItem->product->unit_id,
                'name' => $goodsReceiptItem->product->unit->name,
                'quantity' => 1
            ];
        }

        foreach($productConversions as $conversion) {
            $units[$conversion->product_id][] = [
                'id' => $conversion->unit_id,
                'name' => $conversion->unit->name,
                'quantity' => $conversion->quantity
            ];
        }

        $data = [
            'id' => $id,
            'goodsReceipt' => $goodsReceipt,
            'goodsReceiptItems' => $goodsReceiptItems,
            'products' => $products,
            'rowNumbers' => $rowNumbers,
            'units' => $units ?? [],
        ];

        return view('pages.admin.goods-receipt.edit', $data);
    }

    public function update(GoodsReceiptUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
            $goodsReceipt->update([
                'status' => Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($goodsReceipt->approvals);

            $parentApproval = ApprovalService::createData(
                $goodsReceipt,
                $goodsReceipt->goodsReceiptItems,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            ApprovalService::createData(
                $goodsReceipt,
                $data,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $data['description'],
                $parentApproval->id
            );

            DB::commit();

            return redirect()->route('goods-receipts.index-edit');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('goods-receipts.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy(GoodsReceiptCancelRequest $request, $id) {
        try {
            DB::beginTransaction();

            $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
            $goodsReceipt->update([
                'status' => Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($goodsReceipt->approvals);
            ApprovalService::createData(
                $goodsReceipt,
                $goodsReceipt->goodsReceiptItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            return redirect()->route('goods-receipts.index-edit');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }
}
