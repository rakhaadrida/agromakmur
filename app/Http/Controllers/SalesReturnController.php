<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryOrderCancelRequest;
use App\Http\Requests\DeliveryOrderUpdateRequest;
use App\Http\Requests\SalesReturnCreateRequest;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Notifications\CancelDeliveryOrderNotification;
use App\Notifications\UpdateDeliveryOrderNotification;
use App\Utilities\Constant;
use App\Utilities\Services\AccountReceivableService;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\CommonService;
use App\Utilities\Services\DeliveryOrderService;
use App\Utilities\Services\ProductService;
use App\Utilities\Services\SalesOrderService;
use App\Utilities\Services\SalesReturnService;
use App\Utilities\Services\UserService;
use App\Utilities\Services\WarehouseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesReturnController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $salesReturnStatuses = Constant::SALES_RETURN_STATUSES;
        $salesReturnDeliveryStatuses = Constant::SALES_RETURN_DELIVERY_STATUSES;

        $status = $salesReturnStatuses;
        $deliveryStatus = $salesReturnDeliveryStatuses;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

        if(!empty($filter->delivery_status)) {
            $deliveryStatus = [$filter->delivery_status];
        }

        $baseQuery = SalesReturnService::getBaseQueryIndex();

        $salesReturns = $baseQuery
            ->where('sales_returns.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_returns.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->whereIn('sales_returns.status', $status)
            ->whereIn('sales_returns.delivery_status', $deliveryStatus)
            ->orderByDesc('sales_returns.date')
            ->orderBy('sales_returns.id')
            ->get();

        foreach($salesReturns as $salesReturn) {
            $salesReturn->remaining_quantity = $salesReturn->quantity - $salesReturn->delivered_quantity - $salesReturn->cut_bill_quantity;
        }

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'salesReturnStatuses' => $salesReturnStatuses,
            'salesReturnDeliveryStatuses' => $salesReturnDeliveryStatuses,
            'status' => $filter->status ?? 0,
            'deliveryStatus' => $filter->delivery_status ?? 0,
            'salesReturns' => $salesReturns
        ];

        return view('pages.admin.sales-return.index', $data);
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
        $salesOrders = SalesOrder::query()
            ->where('status' , '!=', Constant::SALES_ORDER_STATUS_WAITING_APPROVAL)
            ->get();

        $data = [
            'date' => $date,
            'customers' => $customers,
            'salesOrders' => $salesOrders,
        ];

        return view('pages.admin.sales-return.create', $data);
    }

    public function store(SalesReturnCreateRequest $request) {
        try {
            DB::beginTransaction();

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $deliveryDate = $request->get('delivery_date');
            $deliveryDate = $deliveryDate ? Carbon::createFromFormat('d-m-Y', $deliveryDate)->format('Y-m-d') : null;

            $request->merge([
                'date' => $date,
                'delivery_date' => $deliveryDate,
                'status' => Constant::SALES_RETURN_STATUS_ACTIVE,
                'user_id' => Auth::user()->id,
            ]);

            $salesReturn = SalesReturn::create($request->all());

            $totalReturnQuantity = 0;
            $totalDeliveredQuantity = 0;
            $totalCutBillQuantity = 0;
            $productIds = $request->get('product_id', []);
            foreach ($productIds as $index => $productId) {
                if(!empty($productId)) {
                    $itemId = $request->get('item_id')[$index];
                    $unitId = $request->get('unit_id')[$index];
                    $quantity = $request->get('quantity')[$index];
                    $realQuantity = $request->get('real_quantity')[$index];
                    $deliveredQuantity = $request->get('delivered_quantity')[$index];
                    $cutBillQuantity = $request->get('cut_bill_quantity')[$index];
                    $actualQuantity = $quantity * $realQuantity;
                    $actualDeliveredQuantity = $deliveredQuantity * $realQuantity;

                    if($quantity > 0) {
                        $salesReturn->salesReturnItems()->create([
                            'sales_order_item_id' => $itemId,
                            'product_id' => $productId,
                            'unit_id' => $unitId,
                            'quantity' => $quantity,
                            'actual_quantity' => $actualQuantity,
                            'delivered_quantity' => $deliveredQuantity,
                            'cut_bill_quantity' => $cutBillQuantity,
                        ]);

                        $totalReturnQuantity += $quantity;
                        $totalDeliveredQuantity += $deliveredQuantity;
                        $totalCutBillQuantity += $cutBillQuantity;

                        $returnWarehouse = WarehouseService::getReturnWarehouse();
                        $productStock = ProductService::getProductStockQuery(
                            $productId,
                            $returnWarehouse->id
                        );

                        $productStock?->increment('stock', $actualQuantity - $actualDeliveredQuantity);

                        if($cutBillQuantity > 0) {
                            $accountReceivable = AccountReceivableService::getAccountReceivableBySalesOrderId($salesReturn->sales_order_id);

                            if($accountReceivable) {
                                $salesOrderItem = SalesOrderService::getSalesOrderItemById($itemId);
                                $total = $salesOrderItem->price * $cutBillQuantity;
                                $discountPercentage = CommonService::calculateDiscountPercentage($salesOrderItem->discount);
                                $discountAmount = ceil(($total * $discountPercentage) / 100);
                                $finalAmount = ceil($total - $discountAmount);

                                $accountReceivable->returns()->create([
                                    'sales_return_id' => $salesReturn->id,
                                    'product_id' => $productId,
                                    'unit_id' => $unitId,
                                    'quantity' => $cutBillQuantity,
                                    'actual_quantity' => $cutBillQuantity * $realQuantity,
                                    'price_id' => $salesOrderItem->price_id,
                                    'price' => $salesOrderItem->price,
                                    'total' => $total,
                                    'discount' => $salesOrderItem->discount,
                                    'discount_amount' => $discountAmount,
                                    'final_amount' => $finalAmount,
                                ]);
                            }
                        }
                    }
                }
            }

            $totalRemainingQuantity = $totalReturnQuantity - $totalDeliveredQuantity - $totalCutBillQuantity;

            $deliveryStatus = Constant::SALES_RETURN_DELIVERY_STATUS_ACTIVE;
            if($totalRemainingQuantity == 0) {
                $deliveryStatus = Constant::SALES_RETURN_DELIVERY_STATUS_COMPLETED;
            } else if($totalDeliveredQuantity > 0 || $totalCutBillQuantity > 0) {
                $deliveryStatus = Constant::SALES_RETURN_DELIVERY_STATUS_ONGOING;
            }

            $salesReturn->update([
                'delivery_status' => $deliveryStatus
            ]);

            DB::commit();

            return redirect()->route('sales-returns.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $salesReturn = SalesReturn::query()->findOrFail($id);
        $salesReturnItems = $salesReturn->salesReturnItems;

        foreach($salesReturnItems as $salesReturnItem) {
            $remainingQuantity = $salesReturnItem->quantity - $salesReturnItem->delivered_quantity - $salesReturnItem->cut_bill_quantity;
            $salesReturnItem->remaining_quantity = $remainingQuantity;
        }

        $rowNumbers = count($salesReturnItems);

        $data = [
            'id' => $id,
            'salesReturn' => $salesReturn,
            'salesReturnItems' => $salesReturnItems,
            'rowNumbers' => $rowNumbers,
        ];

        return view('pages.admin.sales-return.edit', $data);
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

            $users = UserService::getSuperAdminUsers();

            foreach($users as $user) {
                $user->notify(new UpdateDeliveryOrderNotification($deliveryOrder->number, $parentApproval->id));
            }

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
            $approval = ApprovalService::createData(
                $deliveryOrder,
                $deliveryOrder->deliveryOrderItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            $users = UserService::getSuperAdminUsers();

            foreach($users as $user) {
                $user->notify(new CancelDeliveryOrderNotification($deliveryOrder->number, $approval->id));
            }

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
