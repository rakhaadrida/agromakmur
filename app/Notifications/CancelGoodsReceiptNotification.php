<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancelGoodsReceiptNotification extends Notification
{
    use Queueable;

    protected $receiptNumber;
    protected $approvalId;

    /**
     * Create a new notification instance.
     */
    public function __construct($receiptNumber, $approvalId)
    {
        $this->receiptNumber = $receiptNumber;
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
            'subject' => 'Permintaan Pembatalan Barang Masuk - ' . $this->receiptNumber,
            'message' => 'Ada permintaan pembatalan baru untuk barang masuk dengan nomor ' .$this->receiptNumber. '. Klik di sini untuk lihat detail.',
            'approval_id' => $this->approvalId
        ];
    }
}
