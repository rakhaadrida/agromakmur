<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateDeliveryOrderNotification extends Notification
{
    use Queueable;

    protected $deliveryNumber;
    protected $approvalId;

    /**
     * Create a new notification instance.
     */
    public function __construct($deliveryNumber, $approvalId)
    {
        $this->deliveryNumber = $deliveryNumber;
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
            'subject' => 'Permintaan Perubahan Surat Jalan - ' . $this->deliveryNumber,
            'message' => 'Ada permintaan perubahan baru untuk surat jalan dengan nomor ' .$this->deliveryNumber. '. Klik di sini untuk lihat detail.',
            'approval_id' => $this->approvalId
        ];
    }
}
