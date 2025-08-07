<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsReceiptCancelRequest;
use App\Http\Requests\GoodsReceiptUpdateRequest;
use App\Http\Requests\SalesOrderCreateRequest;
use App\Models\Customer;
use App\Models\GoodsReceipt;
use App\Models\Marketing;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\AccountReceivableService;
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

class SalesOrderController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $baseQuery = SalesOrderService::getBaseQueryIndex();

        if($startDate) {
            $baseQuery = $baseQuery->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay());
        }

        if($finalDate) {
            $baseQuery = $baseQuery->where('sales_orders.date', '<=', Carbon::parse($finalDate)->endOfDay());
        }

        $salesOrders = $baseQuery
            ->orderBy('sales_orders.date')
            ->get();

        $salesOrders = SalesOrderService::mapSalesOrderIndex($salesOrders);

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'salesOrders' => $salesOrders
        ];

        return view('pages.admin.sales-order.index', $data);
    }

    public function detail($id) {
        $salesOrder = SalesOrder::query()->findOrFail($id);
        $salesOrderItems = $salesOrder->salesOrderItems;

        if(isWaitingApproval($salesOrder->status) && isApprovalTypeEdit($salesOrder->pendingApproval->type)) {
            $salesOrder = SalesOrderService::mapSalesOrderApproval($salesOrder);
            $salesOrderItems = $salesOrder->salesOrderItems;
        }

        $productWarehouses = [];
        foreach($salesOrderItems as $salesOrderItem) {
            $productWarehouses[$salesOrderItem->product_id][$salesOrderItem->warehouse_id] = $salesOrderItem->quantity;
        }

        $salesOrderItems = $salesOrderItems
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                return (object) [
                    'product_id' => $productId,
                    'product_sku' => $items->first()->product->sku,
                    'product_name' => $items->first()->product->name,
                    'quantity' => $items->sum('quantity'),
                    'unit_id' => $items->first()->unit_id,
                    'unit_name' => $items->first()->unit->name,
                    'price' => $items->first()->price,
                    'total' => $items->sum('total'),
                    'discount' => $items->first()->discount,
                    'discount_amount' => $items->sum('discount_amount'),
                    'final_amount' => $items->sum('final_amount'),
                ];
            })
            ->values();

        $warehouses = Warehouse::query()
            ->where('type', '!=', Constant::WAREHOUSE_TYPE_RETURN)
            ->get();

        $data = [
            'id' => $id,
            'salesOrder' => $salesOrder,
            'salesOrderItems' => $salesOrderItems,
            'productWarehouses' => $productWarehouses,
            'warehouses' => $warehouses,
            'totalWarehouses' => $warehouses->count(),
        ];

        return view('pages.admin.sales-order.detail', $data);
    }

    public function create() {
        $date = Carbon::now()->format('d-m-Y');
        $customers = Customer::all();
        $marketings = Marketing::all();
        $products = Product::all();
        $warehouses = Warehouse::query()
            ->where('type', Constant::WAREHOUSE_TYPE_SECONDARY)
            ->get();

        $rows = range(1, 5);
        $rowNumbers = count($rows);

        $data = [
            'date' => $date,
            'customers' => $customers,
            'marketings' => $marketings,
            'products' => $products,
            'warehouses' => $warehouses,
            'rows' => $rows,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.admin.sales-order.create', $data);
    }

    public function store(SalesOrderCreateRequest $request) {
        try {
            DB::beginTransaction();

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $deliveryDate = $request->get('delivery_date');
            $deliveryDate = Carbon::createFromFormat('d-m-Y', $deliveryDate)->format('Y-m-d');

            $request->merge([
                'date' => $date,
                'delivery_date' => $deliveryDate,
                'discount_amount' => $request->get('invoice_discount') ?? 0,
                'subtotal' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
                'status' => Constant::SALES_ORDER_STATUS_ACTIVE,
                'user_id' => Auth::user()->id,
            ]);

            $salesOrder = SalesOrder::create($request->all());

            $productIds = $request->get('product_id', []);
            $unitIds = $request->get('unit_id', []);
            $realQuantities = $request->get('real_quantity', []);
            $prices = $request->get('price', []);
            $priceIds = $request->get('price_id', []);
            $discounts = $request->get('discount', []);
            $discountProducts = $request->get('discount_product', []);
            $warehouseIdsList = $request->get('warehouse_ids', []);
            $warehouseStocksList = $request->get('warehouse_stocks', []);

            $itemsData = collect($productIds)
                ->map(function ($productId, $index) use (
                    $unitIds, $realQuantities, $prices, $priceIds,
                    $discounts, $discountProducts, $warehouseIdsList, $warehouseStocksList
                ) {
                    if (empty($productId)) return null;

                    return [
                        'product_id' => $productId,
                        'unit_id' => $unitIds[$index],
                        'real_quantity' => $realQuantities[$index],
                        'price' => $prices[$index],
                        'price_id' => $priceIds[$index],
                        'discount' => $discounts[$index],
                        'discount_product' => $discountProducts[$index],
                        'warehouse_ids' => explode(',', $warehouseIdsList[$index] ?? ''),
                        'warehouse_stocks' => explode(',', $warehouseStocksList[$index] ?? ''),
                    ];
                })
                ->filter();

            $subtotal = 0;
            foreach ($itemsData as $item) {
                $totalDiscount = $item['discount_product'];
                $warehouseCount = count($item['warehouse_ids']);

                foreach ($item['warehouse_ids'] as $key => $warehouseId) {
                    $quantity = $item['warehouse_stocks'][$key] ?? 0;
                    $actualQuantity = $quantity * $item['real_quantity'];
                    $total = $quantity * $item['price'];

                    $discountValue = round($item['discount_product'] / $warehouseCount);
                    if ($discountValue < $totalDiscount) {
                        $totalDiscount -= $discountValue;
                    } else {
                        $discountValue = $totalDiscount;
                        $totalDiscount = 0;
                    }

                    $finalAmount = $total - $discountValue;
                    $subtotal += $finalAmount;

                    $salesOrder->salesOrderItems()->create([
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $warehouseId,
                        'unit_id' => $item['unit_id'],
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'price_id' => $item['price_id'],
                        'price' => $item['price'],
                        'total' => $total,
                        'discount' => $item['discount'],
                        'discount_amount' => $discountValue,
                        'final_amount' => $finalAmount
                    ]);

                    ProductService::getProductStockQuery($item['product_id'], $warehouseId)
                        ->decrement('stock', $actualQuantity);
                }
            }

            $totalAfterDiscount = $subtotal - $salesOrder->discount_amount;
            $taxAmount = round($totalAfterDiscount * (10 / 100));
            $grandTotal = (int) $totalAfterDiscount + $taxAmount;

            $salesOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal
            ]);

            AccountReceivableService::createData($salesOrder);

            $parameters = [];
            $route = 'sales-orders.create';

            if($request->get('is_print')) {
                $route = 'sales-orders.print';
                $parameters = ['id' => $salesOrder->id];
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

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexPrint() {
        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.is_printed', 0)
            ->where('goods_receipts.status', '!=', Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL)
            ->orderBy('goods_receipts.date')
            ->get();

        $data = [
            'goodsReceipts' => $goodsReceipts
        ];

        return view('pages.admin.goods-receipt.index-print', $data);
    }

    public function print(Request $request, $id) {
        $filter = (object) $request->all();
        $startNumber = $filter->start_number ?? 0;
        $finalNumber = $filter->final_number ?? 0;

        $printDate = Carbon::parse()->isoFormat('dddd, D MMMM Y');
        $printTime = Carbon::now()->format('H:i:s');
        $baseQuery = GoodsReceiptService::getBaseQueryIndex();

        if($id) {
            $baseQuery = $baseQuery->where('goods_receipts.id', $id);
        } else {
            if($startNumber) {
                $baseQuery = $baseQuery->where('goods_receipts.id', '>=', $startNumber);
            }

            if($finalNumber) {
                $baseQuery = $baseQuery->where('goods_receipts.id', '<=', $finalNumber);
            } else {
                $baseQuery = $baseQuery->where('goods_receipts.id', '<=', $startNumber);
            }
        }

        $goodsReceipts = $baseQuery
            ->where('goods_receipts.is_printed', 0)
            ->get();

        $data = [
            'id' => $id,
            'goodsReceipts' => $goodsReceipts,
            'printDate' => $printDate,
            'printTime' => $printTime,
            'startNumber' => $startNumber,
            'finalNumber' => $finalNumber,
            'rowNumbers' => 35
        ];

        return view('pages.admin.goods-receipt.print', $data);
    }

    public function afterPrint(Request $request, $id) {
        try {
            DB::beginTransaction();

            $filter = (object) $request->all();
            $startNumber = $filter->start_number ?? 0;
            $finalNumber = $filter->final_number ?? 0;

            $baseQuery = GoodsReceipt::query();

            if($id) {
                $baseQuery = $baseQuery->where('goods_receipts.id', $id);
            } else {
                if($startNumber) {
                    $baseQuery = $baseQuery->where('goods_receipts.id', '>=', $startNumber);
                }

                if($finalNumber) {
                    $baseQuery = $baseQuery->where('goods_receipts.id', '<=', $finalNumber);
                } else {
                    $baseQuery = $baseQuery->where('goods_receipts.id', '<=', $startNumber);
                }
            }

            $goodsReceipts = $baseQuery
                ->where('goods_receipts.is_printed', 0)
                ->get();

            foreach ($goodsReceipts as $goodsReceipt) {
                $goodsReceipt->update(['is_printed' => 1]);
            }

            $route = $id ? 'goods-receipts.create' : 'goods-receipts.index-print';

            DB::commit();

            return redirect()->route($route);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }
}
