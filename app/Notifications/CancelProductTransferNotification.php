<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancelProductTransferNotification extends Notification
{
    use Queueable;

    protected $transferNumber;
    protected $approvalId;

    /**
     * Create a new notification instance.
     */
    public function __construct($transferNumber, $approvalId)
    {
        $this->transferNumber = $transferNumber;
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
            'subject' => 'Permintaan Pembatalan Produk Transfer - ' . $this->transferNumber,
            'message' => 'Ada permintaan pembatalan baru untuk produk transfer dengan nomor ' .$this->transferNumber. '. Klik di sini untuk lihat detail.',
            'approval_id' => $this->approvalId
        ];
    }
}
