<?php

namespace App\Http\Controllers;

use App\Exports\DeliveryOrderExport;
use App\Http\Requests\DeliveryOrderCancelRequest;
use App\Http\Requests\DeliveryOrderCreateRequest;
use App\Http\Requests\DeliveryOrderUpdateRequest;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Notifications\CancelDeliveryOrderNotification;
use App\Notifications\UpdateDeliveryOrderNotification;
use App\Utilities\Constant;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\CommonService;
use App\Utilities\Services\DeliveryOrderService;
use App\Utilities\Services\NumberSettingService;
use App\Utilities\Services\SalesOrderService;
use App\Utilities\Services\UserService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DeliveryOrderController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? null;

        if(!$finalDate) {
            $finalDate = $startDate;
        }

        $baseQuery = DeliveryOrderService::getBaseQueryIndex();

        if($startDate) {
            $baseQuery = $baseQuery->where('delivery_orders.date', '>=',  Carbon::parse($startDate)->startOfDay());
        }

        if($finalDate) {
            $baseQuery = $baseQuery->where('delivery_orders.date', '<=', Carbon::parse($finalDate)->endOfDay());
        }

        $deliveryOrders = $baseQuery
            ->orderByDesc('delivery_orders.date')
            ->get();

        $deliveryOrders = DeliveryOrderService::mapDeliveryOrderIndex($deliveryOrders);

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'deliveryOrders' => $deliveryOrders
        ];

        return view('pages.admin.delivery-order.index', $data);
    }

    public function detail($id) {
        $deliveryOrder = DeliveryOrder::query()->findOrFail($id);
        $deliveryOrder->revision = ApprovalService::getRevisionCountBySubject(DeliveryOrder::class, [$deliveryOrder->id]);
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
        $salesOrders = SalesOrder::query()
            ->where('status' , '!=', Constant::SALES_ORDER_STATUS_WAITING_APPROVAL)
            ->where('delivery_status', '!=', Constant::SALES_ORDER_DELIVERY_STATUS_COMPLETED)
            ->get();

        $rows = range(1, 5);
        $rowNumbers = count($rows);

        $data = [
            'date' => $date,
            'salesOrders' => $salesOrders,
            'rows' => $rows,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.admin.delivery-order.create', $data);
    }

    public function store(DeliveryOrderCreateRequest $request) {
        try {
            DB::beginTransaction();

            $number = $request->get('number');
            if($request->get('is_generated_number')) {
                $number = NumberSettingService::generateNumber(Constant::NUMBER_SETTING_KEY_DELIVERY_ORDER, $request->get('branch_id'));
            }

            $date = $request->get('date');
            $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

            $request->merge([
                'number' => $number,
                'date' => $date,
                'status' => Constant::DELIVERY_ORDER_STATUS_ACTIVE,
                'user_id' => Auth::user()->id,
            ]);

            $deliveryOrder = DeliveryOrder::create($request->all());

            $totalOrderQuantity = 0;
            $totalDeliveredQuantity = 0;
            $productIds = $request->get('product_id', []);
            foreach ($productIds as $index => $productId) {
                if(!empty($productId)) {
                    $unitId = $request->get('unit_id')[$index];
                    $orderQuantity = $request->get('order_quantity')[$index];
                    $quantity = $request->get('quantity')[$index];
                    $actualQuantity = $request->get('real_quantity')[$index];

                    $deliveryOrder->deliveryOrderItems()->create([
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                    ]);

                    $totalOrderQuantity += $orderQuantity;
                }
            }

            $deliveredQuantities = DeliveryOrderService::getDeliveryQuantityBySalesOrderProductIds($deliveryOrder->sales_order_id, $productIds);
            foreach($deliveredQuantities as $deliveredQuantity) {
                $totalDeliveredQuantity += $deliveredQuantity->quantity;
            }

            $deliveryStatus = Constant::SALES_ORDER_DELIVERY_STATUS_ON_PROGRESS;
            if($totalOrderQuantity == $totalDeliveredQuantity) {
                $deliveryStatus = Constant::SALES_ORDER_DELIVERY_STATUS_COMPLETED;
            }

            $deliveryOrder->salesOrder()->update([
                'delivery_status' => $deliveryStatus
            ]);

            $parameters = [];
            $route = 'delivery-orders.create';

            if($request->get('is_print')) {
                $route = 'delivery-orders.print';
                $parameters = ['id' => $deliveryOrder->id];
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

    public function edit(Request $request, $id) {
        $deliveryOrder = DeliveryOrder::query()->findOrFail($id);
        $deliveryOrder->revision = ApprovalService::getRevisionCountBySubject(DeliveryOrder::class, [$deliveryOrder->id]);
        $deliveryOrderItems = $deliveryOrder->deliveryOrderItems;

        if(isWaitingApproval($deliveryOrder->status) && isApprovalTypeEdit($deliveryOrder->pendingApproval->type)) {
            $deliveryOrder = DeliveryOrderService::mapDeliveryOrderApproval($deliveryOrder);
            $deliveryOrderItems = $deliveryOrder->deliveryOrderItems;
        }

        $productIds = $deliveryOrderItems->pluck('product_id')->toArray();
        $orderQuantities = SalesOrderService::getSalesOrderQuantityBySalesOrderProductIds($deliveryOrder->sales_order_id, $productIds);

        $mapOrderQuantityByProductId = [];
        foreach($orderQuantities as $orderQuantity) {
            $mapOrderQuantityByProductId[$orderQuantity->product_id] = $orderQuantity->quantity;
        }

        $deliveredQuantities = DeliveryOrderService::getDeliveryQuantityBySalesOrderProductIds($deliveryOrder->sales_order_id, $productIds);
        $mapDeliveredQuantityByProductId = [];
        foreach($deliveredQuantities as $deliveredQuantity) {
            $mapDeliveredQuantityByProductId[$deliveredQuantity->product_id] = $deliveredQuantity->quantity;
        }

        foreach($deliveryOrderItems as $deliveryOrderItem) {
            $orderQuantity = $mapOrderQuantityByProductId[$deliveryOrderItem->product_id];
            $deliveredQuantity = $mapDeliveredQuantityByProductId[$deliveryOrderItem->product_id];
            $remainingQuantity = $orderQuantity - $deliveredQuantity + $deliveryOrderItem->quantity;

            $deliveryOrderItem->order_quantity = $orderQuantity;
            $deliveryOrderItem->delivered_quantity = $deliveredQuantity;
            $deliveryOrderItem->remaining_quantity = $remainingQuantity;
        }

        $rowNumbers = count($deliveryOrderItems);

        $data = [
            'id' => $id,
            'deliveryOrder' => $deliveryOrder,
            'deliveryOrderItems' => $deliveryOrderItems,
            'rowNumbers' => $rowNumbers,
            'startDate' => $request->start_date ?? null,
            'finalDate' => $request->final_date ?? null,
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

            ApprovalService::deleteData($deliveryOrder->pendingApprovals);

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

            $users = UserService::getSuperAdminUsers($deliveryOrder->branch_id);

            foreach($users as $user) {
                $user->notify(new UpdateDeliveryOrderNotification($deliveryOrder->number, $parentApproval->id));
            }

            $params = [
                'start_date' => $request->get('start_date', null),
                'final_date' => $request->get('final_date', null),
            ];

            return redirect()->route('delivery-orders.index', $params);
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

            ApprovalService::deleteData($deliveryOrder->pendingApprovals);
            $approval = ApprovalService::createData(
                $deliveryOrder,
                $deliveryOrder->deliveryOrderItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            DB::commit();

            $users = UserService::getSuperAdminUsers($deliveryOrder->branch_id);

            foreach($users as $user) {
                $user->notify(new CancelDeliveryOrderNotification($deliveryOrder->number, $approval->id));
            }

            $params = [
                'start_date' => $request->get('start_date', null),
                'final_date' => $request->get('final_date', null),
            ];

            return redirect()->route('delivery-orders.index', $params);
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

    public function indexPrintAjax(Request $request) {
        $filter = (object) $request->all();
        $isPrinted = $filter->is_printed;

        $baseQuery = DeliveryOrderService::getBaseQueryIndex();

        if($isPrinted) {
            $baseQuery = $baseQuery
                ->where('delivery_orders.is_printed', 1)
                ->orderByDesc('delivery_orders.date')
                ->orderByDesc('delivery_orders.id');
        } else {
            $baseQuery = $baseQuery
                ->where('delivery_orders.is_printed', 0)
                ->orderBy('delivery_orders.date');
        }

        $deliveryOrders = $baseQuery
            ->where('delivery_orders.status', '!=', Constant::DELIVERY_ORDER_STATUS_WAITING_APPROVAL)
            ->get();

        return response()->json([
            'data' => $deliveryOrders,
        ]);
    }

    public function print(Request $request, $id) {
        $filter = (object) $request->all();

        $isPrinted = $filter->is_printed;
        $startNumber = $isPrinted ? $filter->start_number_printed : $filter->start_number;
        $finalNumber = $isPrinted ? $filter->final_number_printed : $filter->final_number;
        $startOperator = $isPrinted ? '<=' : '>=';
        $finalOperator = $isPrinted ? '>=' : '<=';

        $printDate = Carbon::parse()->isoFormat('dddd, D MMMM Y');
        $printTime = Carbon::now()->format('H:i:s');
        $baseQuery = DeliveryOrderService::getBaseQueryIndex();

        if($id) {
            $baseQuery = $baseQuery->where('delivery_orders.id', $id);
        } else {
            if($startNumber) {
                $baseQuery = $baseQuery->where('delivery_orders.id', $startOperator, $startNumber);
            }

            if($finalNumber) {
                $baseQuery = $baseQuery->where('delivery_orders.id', $finalOperator, $finalNumber);
            } else {
                $baseQuery = $baseQuery->where('delivery_orders.id', $finalOperator, $startNumber);
            }
        }

        if($isPrinted) {
            $baseQuery = $baseQuery->where('delivery_orders.is_printed', 1);
        } else {
            $baseQuery = $baseQuery->where('delivery_orders.is_printed', 0);
        }

        $deliveryOrders = $baseQuery
            ->where('delivery_orders.status', '!=', Constant::DELIVERY_ORDER_STATUS_WAITING_APPROVAL)
            ->get();

        $itemsPerPage = 14;
        foreach ($deliveryOrders as $key => $deliveryOrder) {
            CommonService::paginatePrintPages($deliveryOrder, $deliveryOrder->deliveryOrderItems, $itemsPerPage);
        }

        $data = [
            'id' => $id,
            'deliveryOrders' => $deliveryOrders,
            'printDate' => $printDate,
            'printTime' => $printTime,
            'startNumber' => $startNumber,
            'finalNumber' => $finalNumber,
            'itemsPerPage' => $itemsPerPage
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

    public function export(Request $request) {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new DeliveryOrderExport($request), 'Daftar_Surat_Jalan_'.$fileDate.'.xlsx');
    }

    public function pdf(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? null;

        if(!$finalDate) {
            $finalDate = $startDate;
        }

        $baseQuery = DeliveryOrderService::getBaseQueryIndex();

        $baseQuery = DeliveryOrderService::getAdditionalQueryIndex($baseQuery);

        $deliveryOrders = $baseQuery
            ->where('delivery_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('delivery_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderByDesc('delivery_orders.date')
            ->get();

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'deliveryOrders' => $deliveryOrders,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.admin.delivery-order.pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Daftar_Surat_Jalan_'.$fileDate.'.pdf');
    }

    public function generateNumberAjax(Request $request) {
        $filter = (object) $request->all();

        $number = NumberSettingService::currentNumber(Constant::NUMBER_SETTING_KEY_DELIVERY_ORDER, $filter->branch_id);

        return response()->json([
            'number' => $number
        ]);
    }
}
