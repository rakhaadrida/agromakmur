<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesReturnCancelRequest;
use App\Http\Requests\SalesReturnCreateRequest;
use App\Http\Requests\SalesReturnUpdateRequest;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Notifications\CancelSalesReturnNotification;
use App\Utilities\Constant;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\DeliveryOrderService;
use App\Utilities\Services\SalesOrderService;
use App\Utilities\Services\SalesReturnService;
use App\Utilities\Services\UserService;
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
            SalesReturnService::createItemData($salesReturn, $request);

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

        $productIds = $salesReturnItems->pluck('product_id')->toArray();
        $orderQuantities = SalesOrderService::getSalesOrderQuantityBySalesOrderProductIds($salesReturn->sales_order_id, $productIds);

        $mapOrderQuantityByProductId = [];
        foreach($orderQuantities as $orderQuantity) {
            $mapOrderQuantityByProductId[$orderQuantity->product_id] = $orderQuantity->quantity;
        }

        foreach($salesReturnItems as $salesReturnItem) {
            $remainingQuantity = $salesReturnItem->quantity - $salesReturnItem->delivered_quantity - $salesReturnItem->cut_bill_quantity;
            $salesReturnItem->remaining_quantity = $remainingQuantity;
            $salesReturnItem->order_quantity = $mapOrderQuantityByProductId[$salesReturnItem->product_id] ?? 0;
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

    public function update(SalesReturnUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $deliveryDate = $request->get('delivery_date');
            $deliveryDate = $deliveryDate ? Carbon::createFromFormat('d-m-Y', $deliveryDate)->format('Y-m-d') : null;

            $salesReturn = SalesReturn::query()->findOrFail($id);
            $salesReturn->update([
                'delivery_date' => $deliveryDate
            ]);

            $salesReturn->accountReceivableReturns()->delete();

            SalesReturnService::deleteItemData($salesReturn->salesReturnItems);
            SalesReturnService::createItemData($salesReturn, $request);

            DB::commit();

            return redirect()->route('sales-returns.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('sales-returns.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy(SalesReturnCancelRequest $request, $id) {
        try {
            DB::beginTransaction();

            $salesReturn = SalesReturn::query()->findOrFail($id);
            $salesReturn->update([
                'status' => Constant::SALES_RETURN_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($salesReturn->approvals);
            $approval = ApprovalService::createData(
                $salesReturn,
                $salesReturn->salesReturnItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            $users = UserService::getSuperAdminUsers();

            foreach($users as $user) {
                $user->notify(new CancelSalesReturnNotification($salesReturn->number, $approval->id));
            }

            return redirect()->route('sales-returns.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('sales-returns.edit', $id)->withInput()->withErrors([
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
