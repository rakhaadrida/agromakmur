<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryOrderCancelRequest;
use App\Http\Requests\DeliveryOrderUpdateRequest;
use App\Http\Requests\SalesOrderCreateRequest;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Marketing;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\AccountReceivableService;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\DeliveryOrderService;
use App\Utilities\Services\ProductService;
use App\Utilities\Services\SalesOrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryOrderController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? null;

        if(!$finalDate) {
            $finalDate = $startDate;
        }

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
        $deliveryOrder = DeliveryOrder::query()->findOrFail($id);
        $deliveryOrderItems = $deliveryOrder->deliveryOrderItems;

        if(isWaitingApproval($deliveryOrder->status) && isApprovalTypeEdit($deliveryOrder->pendingApproval->type)) {
            $deliveryOrder = DeliveryOrderService::mapDeliveryOrderApproval($deliveryOrder);
            $deliveryOrderItems = $deliveryOrder->deliveryOrderItems;
        }

        $data = [
            'id' => $id,
            'deliveryOrder' => $deliveryOrder,
            'deliveryOrderItems' => $deliveryOrderItems,
        ];

        return view('pages.admin.delivery-order.detail', $data);
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
        $customerId = $filter->customer_id ?? null;

        if(!$number && !$customerId && !$startDate && !$finalDate) {
            $startDate = Carbon::now()->format('d-m-Y');
            $finalDate = Carbon::now()->format('d-m-Y');
        }

        $customers = Customer::all();
        $baseQuery = DeliveryOrderService::getBaseQueryIndex();

        if($startDate) {
            $baseQuery = $baseQuery->where('delivery_orders.date', '>=',  Carbon::parse($startDate)->startOfDay());
        }

        if($finalDate) {
            $baseQuery = $baseQuery->where('delivery_orders.date', '<=', Carbon::parse($finalDate)->endOfDay());
        }

        if($number) {
            $baseQuery = $baseQuery->where('delivery_orders.number', $number);
        }

        if($customerId) {
            $baseQuery = $baseQuery->where('delivery_orders.customer_id', $customerId);
        }

        $deliveryOrders = $baseQuery
            ->orderByDesc('delivery_orders.date')
            ->orderByDesc('delivery_orders.id')
            ->get();

        $deliveryOrders = DeliveryOrderService::mapDeliveryOrderIndex($deliveryOrders, true);

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'number' => $number,
            'customerId' => $customerId,
            'customers' => $customers,
            'deliveryOrders' => $deliveryOrders,
        ];

        return view('pages.admin.delivery-order.index-edit', $data);
    }

    public function edit($id) {
        $deliveryOrder = DeliveryOrder::query()->findOrFail($id);
        $deliveryOrderItems = $deliveryOrder->deliveryOrderItems;

        if(isWaitingApproval($deliveryOrder->status) && isApprovalTypeEdit($deliveryOrder->pendingApproval->type)) {
            $deliveryOrder = DeliveryOrderService::mapDeliveryOrderApproval($deliveryOrder);
            $deliveryOrderItems = $deliveryOrder->salesOrderItems;
        }

        $rowNumbers = count($deliveryOrderItems);

        $data = [
            'id' => $id,
            'deliveryOrder' => $deliveryOrder,
            'deliveryOrderItems' => $deliveryOrderItems,
            'rowNumbers' => $rowNumbers,
        ];

        return view('pages.admin.delivery-order.edit', $data);
    }

    public function update(DeliveryOrderUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $deliveryOrder = DeliveryOrder::query()->findOrFail($id);
            $deliveryOrder->update([
                'status' => Constant::DELIVERY_ORDER_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($deliveryOrder->approvals);

            $parentApproval = ApprovalService::createData(
                $deliveryOrder,
                $deliveryOrder->deliveryOrderItems,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            ApprovalService::createData(
                $deliveryOrder,
                $data,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $data['description'],
                $parentApproval->id,
            );

            DB::commit();

            return redirect()->route('delivery-orders.index-edit');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('delivery-orders.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy(DeliveryOrderCancelRequest $request, $id) {
        try {
            DB::beginTransaction();

            $deliveryOrder = DeliveryOrder::query()->findOrFail($id);
            $deliveryOrder->update([
                'status' => Constant::DELIVERY_ORDER_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($deliveryOrder->approvals);
            ApprovalService::createData(
                $deliveryOrder,
                $deliveryOrder->deliveryOrderItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            return redirect()->route('delivery-orders.index-edit');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while deleting data'
            ]);
        }
    }

    public function indexPrint() {
        $baseQuery = DeliveryOrderService::getBaseQueryIndex();

        $deliveryOrders = $baseQuery
            ->where('delivery_orders.is_printed', 0)
            ->where('delivery_orders.status', '!=', Constant::SALES_ORDER_STATUS_WAITING_APPROVAL)
            ->orderBy('delivery_orders.date')
            ->get();

        $data = [
            'deliveryOrders' => $deliveryOrders
        ];

        return view('pages.admin.delivery-order.index-print', $data);
    }

    public function print(Request $request, $id) {
        $filter = (object) $request->all();
        $startNumber = $filter->start_number ?? 0;
        $finalNumber = $filter->final_number ?? 0;

        $printDate = Carbon::parse()->isoFormat('dddd, D MMMM Y');
        $printTime = Carbon::now()->format('H:i:s');
        $baseQuery = DeliveryOrderService::getBaseQueryIndex();

        if($id) {
            $baseQuery = $baseQuery->where('delivery_orders.id', $id);
        } else {
            if($startNumber) {
                $baseQuery = $baseQuery->where('delivery_orders.id', '>=', $startNumber);
            }

            if($finalNumber) {
                $baseQuery = $baseQuery->where('delivery_orders.id', '<=', $finalNumber);
            } else {
                $baseQuery = $baseQuery->where('delivery_orders.id', '<=', $startNumber);
            }
        }

        $deliveryOrders = $baseQuery
            ->where('delivery_orders.is_printed', 0)
            ->where('delivery_orders.status', '!=', Constant::DELIVERY_ORDER_STATUS_WAITING_APPROVAL)
            ->get();

        foreach ($deliveryOrders as $deliveryOrder) {
            $totalPage = ceil(($deliveryOrder->deliveryOrderItems->count()) / 15);
            $deliveryOrder->total_page = $totalPage;
            $deliveryOrder->total_rows = $deliveryOrder->deliveryOrderItems->count();
        }

        $data = [
            'id' => $id,
            'deliveryOrders' => $deliveryOrders,
            'printDate' => $printDate,
            'printTime' => $printTime,
            'startNumber' => $startNumber,
            'finalNumber' => $finalNumber,
            'rowNumbers' => 35
        ];

        return view('pages.admin.delivery-order.print', $data);
    }

    public function afterPrint(Request $request, $id) {
        try {
            DB::beginTransaction();

            $filter = (object) $request->all();
            $startNumber = $filter->start_number ?? 0;
            $finalNumber = $filter->final_number ?? 0;

            $baseQuery = DeliveryOrder::query();

            if($id) {
                $baseQuery = $baseQuery->where('delivery_orders.id', $id);
            } else {
                if($startNumber) {
                    $baseQuery = $baseQuery->where('delivery_orders.id', '>=', $startNumber);
                }

                if($finalNumber) {
                    $baseQuery = $baseQuery->where('delivery_orders.id', '<=', $finalNumber);
                } else {
                    $baseQuery = $baseQuery->where('delivery_orders.id', '<=', $startNumber);
                }
            }

            $deliveryOrders = $baseQuery
                ->where('delivery_orders.is_printed', 0)
                ->where('delivery_orders.status', '!=', Constant::DELIVERY_ORDER_STATUS_WAITING_APPROVAL)
                ->get();

            foreach ($deliveryOrders as $deliveryOrder) {
                $deliveryOrder->update([
                    'is_printed' => 1,
                    'print_count' => $deliveryOrder->print_count + 1
                ]);
            }

            $route = $id ? 'delivery-orders.create' : 'delivery-orders.index-print';

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
