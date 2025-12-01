<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateSalesOrderNotification extends Notification
{
    use Queueable;

    protected $orderNumber;
    protected $approvalId;

    /**
     * Create a new notification instance.
     */
    public function __construct($orderNumber, $approvalId)
    {
        $this->orderNumber = $orderNumber;
        $this->approvalId = $approvalId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        //
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => 'Permintaan Perubahan Sales Order - ' . $this->orderNumber,
            'message' => 'Ada permintaan perubahan baru untuk sales order dengan nomor ' .$this->orderNumber. '. Klik di sini untuk lihat detail.',
            'approval_id' => $this->approvalId
        ];
    }
}
