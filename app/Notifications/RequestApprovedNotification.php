<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestApprovedNotification extends Notification
{
    use Queueable;

    protected $transactionNumber;
    protected $type;
    protected $approvalId;

    /**
     * Create a new notification instance.
     */
    public function __construct($transactionNumber, $type, $approvalId)
    {
        $this->transactionNumber = $transactionNumber;
        $this->type = $type;
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
            'subject' => 'Permintaan Disetujui - ' . $this->transactionNumber,
            'message' => 'Perubahan untuk ' . $this->type . ' dengan nomor ' . $this->transactionNumber . ' telah disetujui. Klik di sini untuk lihat detail.',
            'approval_id' => $this->approvalId
        ];
    }
}
