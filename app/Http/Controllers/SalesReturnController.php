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
use App\Utilities\Services\NumberSettingService;
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

    public function show($id) {
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

        $data = [
            'id' => $id,
            'salesReturn' => $salesReturn,
            'salesReturnItems' => $salesReturnItems
        ];

        return view('pages.admin.sales-return.detail', $data);
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

            $number = $request->get('number');
            if($request->get('is_generated_number')) {
                $number = NumberSettingService::generateNumber(Constant::NUMBER_SETTING_KEY_SALES_RETURN, $request->get('branch_id'));
            }

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $deliveryDate = $request->get('delivery_date');
            $deliveryDate = $deliveryDate ? Carbon::createFromFormat('d-m-Y', $deliveryDate)->format('Y-m-d') : null;

            $request->merge([
                'number' => $number,
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

            ApprovalService::deleteData($salesReturn->pendingApprovals);
            $approval = ApprovalService::createData(
                $salesReturn,
                $salesReturn->salesReturnItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            $users = UserService::getSuperAdminUsers($salesReturn->branch_id);

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

    public function generateNumberAjax(Request $request) {
        $filter = (object) $request->all();

        $number = NumberSettingService::currentNumber(Constant::NUMBER_SETTING_KEY_SALES_RETURN, $filter->branch_id);

        return response()->json([
            'number' => $number
        ]);
    }
}
