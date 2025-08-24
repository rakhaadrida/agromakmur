<?php

namespace App\Utilities\Services;

use App\Models\User;
use App\Notifications\CancelDeliveryOrderNotification;
use App\Notifications\CancelGoodsReceiptNotification;
use App\Notifications\CancelProductTransferNotification;
use App\Notifications\CancelSalesOrderNotification;
use App\Notifications\UpdateDeliveryOrderNotification;
use App\Notifications\UpdateGoodsReceiptNotification;
use App\Notifications\UpdateSalesOrderNotification;
use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public static function getRequestNotificationTypes() {
        return [
            UpdateGoodsReceiptNotification::class,
            UpdateSalesOrderNotification::class,
            UpdateDeliveryOrderNotification::class,
            CancelGoodsReceiptNotification::class,
            CancelProductTransferNotification::class,
            CancelSalesOrderNotification::class,
            CancelDeliveryOrderNotification::class
        ];
    }

    public static function markAsReadRequestNotification($id) {
        $user = User::query()->findOrFail(Auth::id());

        $notificationTypes = NotificationService::getRequestNotificationTypes();
        $notifications = $user->notifications()
            ->whereIn('type', $notificationTypes)
            ->where('data->approval_id', $id)
            ->whereNull('read_at')
            ->get();

        foreach($notifications as $notification) {
            $notification->markAsRead();
        }

        return true;
    }
}
