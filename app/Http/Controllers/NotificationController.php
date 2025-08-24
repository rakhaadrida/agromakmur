<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\DeliveryOrder;
use App\Models\GoodsReceipt;
use App\Models\ProductTransfer;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\SalesOrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index() {
        $user = User::query()->findOrFail(Auth::user()->id);

        $notifications = $user->unreadNotifications;

        $data = [
            'notifications' => $notifications
        ];

        return view('pages.admin.notification.index', $data);
    }

    public function show(Request $request, $id) {
        $filter = (object) $request->all();
        $notificationId = $filter->notification_id ?? null;

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
            'notificationId' => $notificationId,
            'approval' => $approval,
            'approvalItems' => $approvalItems,
            'childData' => $childData,
            'warehouses' => $warehouses,
            'totalWarehouses' => $totalWarehouses,
            'productWarehouses' => $productWarehouses ?? [],
            'childProductWarehouses' => $childProductWarehouses ?? [],
        ];

        return view('pages.admin.notification.detail', $data);
    }

    public function update(Request $request, $id) {
        try {
            DB::beginTransaction();

            $notification = DatabaseNotification::query()->findOrFail($id);
            $notification->read_at = Carbon::now();
            $notification->save();

            DB::commit();

            return redirect()->route('notifications.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function readAll(Request $request) {
        try {
            DB::beginTransaction();

            $user = User::query()->findOrFail(Auth::id());
            $user->unreadNotifications->markAsRead();

            DB::commit();

            return redirect()->route('notifications.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }
}
