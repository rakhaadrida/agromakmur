<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReturnCancelRequest;
use App\Http\Requests\PurchaseReturnCreateRequest;
use App\Http\Requests\PurchaseReturnUpdateRequest;
use App\Models\DeliveryOrder;
use App\Models\GoodsReceipt;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Notifications\CancelPurchaseReturnNotification;
use App\Utilities\Constant;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\DeliveryOrderService;
use App\Utilities\Services\NumberSettingService;
use App\Utilities\Services\PurchaseReturnService;
use App\Utilities\Services\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseReturnController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $purchaseReturnStatuses = Constant::PURCHASE_RETURN_STATUSES;
        $purchaseReturnReceiptStatuses = Constant::PURCHASE_RETURN_RECEIPT_STATUSES;

        $status = $purchaseReturnStatuses;
        $receiptStatus = $purchaseReturnReceiptStatuses;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

        if(!empty($filter->receipt_status)) {
            $receiptStatus = [$filter->receipt_status];
        }

        $baseQuery = PurchaseReturnService::getBaseQueryIndex();

        if($startDate) {
            $baseQuery = $baseQuery->where('purchase_returns.date', '>=',  Carbon::parse($startDate)->startOfDay());
        }

        if($finalDate) {
            $baseQuery = $baseQuery->where('purchase_returns.date', '<=', Carbon::parse($finalDate)->endOfDay());
        }

        $purchaseReturns = $baseQuery
            ->whereIn('purchase_returns.status', $status)
            ->whereIn('purchase_returns.receipt_status', $receiptStatus)
            ->orderByDesc('purchase_returns.date')
            ->orderBy('purchase_returns.id')
            ->get();

        foreach($purchaseReturns as $purchaseReturn) {
            $purchaseReturn->remaining_quantity = $purchaseReturn->quantity - $purchaseReturn->received_quantity - $purchaseReturn->cut_bill_quantity;
        }

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'purchaseReturnStatuses' => $purchaseReturnStatuses,
            'purchaseReturnReceiptStatuses' => $purchaseReturnReceiptStatuses,
            'status' => $filter->status ?? 0,
            'receiptStatus' => $filter->receipt_status ?? 0,
            'purchaseReturns' => $purchaseReturns
        ];

        return view('pages.admin.purchase-return.index', $data);
    }

    public function show($id) {
        $purchaseReturn = PurchaseReturn::query()->findOrFail($id);
        $purchaseReturnItems = $purchaseReturn->purchaseReturnItems;

        foreach($purchaseReturnItems as $purchaseReturnItem) {
            $remainingQuantity = $purchaseReturnItem->quantity - $purchaseReturnItem->received_quantity - $purchaseReturnItem->cut_bill_quantity;
            $purchaseReturnItem->remaining_quantity = $remainingQuantity;
            $purchaseReturnItem->receipt_quantity = $purchaseReturnItem->goodsReceiptItem->quantity;
        }

        $data = [
            'id' => $id,
            'purchaseReturn' => $purchaseReturn,
            'purchaseReturnItems' => $purchaseReturnItems,
        ];

        return view('pages.admin.purchase-return.detail', $data);
    }

    public function create() {
        $date = Carbon::now()->format('d-m-Y');
        $suppliers = Supplier::all();
        $goodsReceipts = GoodsReceipt::query()
            ->where('status' , '!=', Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL)
            ->get();

        $data = [
            'date' => $date,
            'suppliers' => $suppliers,
            'goodsReceipts' => $goodsReceipts,
        ];

        return view('pages.admin.purchase-return.create', $data);
    }

    public function store(PurchaseReturnCreateRequest $request) {
        try {
            DB::beginTransaction();

            $number = $request->get('number');
            if($request->get('is_generated_number')) {
                $number = NumberSettingService::generateNumber(Constant::NUMBER_SETTING_KEY_PURCHASE_RETURN, $request->get('branch_id'));
            }

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $receivedDate = $request->get('received_date');
            $receivedDate = $receivedDate ? Carbon::createFromFormat('d-m-Y', $receivedDate)->format('Y-m-d') : null;

            $request->merge([
                'number' => $number,
                'date' => $date,
                'received_date' => $receivedDate,
                'status' => Constant::PURCHASE_RETURN_STATUS_ACTIVE,
                'user_id' => Auth::user()->id,
            ]);

            $purchaseReturn = PurchaseReturn::create($request->all());
            PurchaseReturnService::createItemData($purchaseReturn, $request);

            DB::commit();

            return redirect()->route('purchase-returns.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function edit($id) {
        $purchaseReturn = PurchaseReturn::query()->findOrFail($id);
        $purchaseReturnItems = $purchaseReturn->purchaseReturnItems;

        foreach($purchaseReturnItems as $purchaseReturnItem) {
            $remainingQuantity = $purchaseReturnItem->quantity - $purchaseReturnItem->received_quantity - $purchaseReturnItem->cut_bill_quantity;
            $purchaseReturnItem->remaining_quantity = $remainingQuantity;
            $purchaseReturnItem->receipt_quantity = $purchaseReturnItem->goodsReceiptItem->quantity;
        }

        $rowNumbers = count($purchaseReturnItems);

        $data = [
            'id' => $id,
            'purchaseReturn' => $purchaseReturn,
            'purchaseReturnItems' => $purchaseReturnItems,
            'rowNumbers' => $rowNumbers,
        ];

        return view('pages.admin.purchase-return.edit', $data);
    }

    public function update(PurchaseReturnUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $receivedDate = $request->get('received_date');
            $receivedDate = $receivedDate ? Carbon::createFromFormat('d-m-Y', $receivedDate)->format('Y-m-d') : null;

            $purchaseReturn = PurchaseReturn::query()->findOrFail($id);
            $purchaseReturn->update([
                'received_date' => $receivedDate
            ]);

            $purchaseReturn->accountPayableReturns()->delete();

            PurchaseReturnService::deleteItemData($purchaseReturn->purchaseReturnItems);
            PurchaseReturnService::createItemData($purchaseReturn, $request);

            DB::commit();

            return redirect()->route('purchase-returns.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('purchase-returns.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function destroy(PurchaseReturnCancelRequest $request, $id) {
        try {
            DB::beginTransaction();

            $purchaseReturn = PurchaseReturn::query()->findOrFail($id);
            $purchaseReturn->update([
                'status' => Constant::PURCHASE_RETURN_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($purchaseReturn->pendingApprovals);
            $approval = ApprovalService::createData(
                $purchaseReturn,
                $purchaseReturn->purchaseReturnItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            $users = UserService::getSuperAdminUsers();

            foreach($users as $user) {
                $user->notify(new CancelPurchaseReturnNotification($purchaseReturn->number, $approval->id));
            }

            return redirect()->route('purchase-returns.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('purchase-returns.edit', $id)->withInput()->withErrors([
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

    public function generateNumberAjax(Request $request) {
        $filter = (object) $request->all();

        $number = NumberSettingService::currentNumber(Constant::NUMBER_SETTING_KEY_PURCHASE_RETURN, $filter->branch_id);

        return response()->json([
            'number' => $number
        ]);
    }
}
