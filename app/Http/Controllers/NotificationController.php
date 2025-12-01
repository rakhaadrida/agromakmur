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
