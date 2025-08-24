<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancelDeliveryOrderNotification extends Notification
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
            'subject' => 'Cancel Delivery Order Request - ' . $this->deliveryNumber,
            'message' => 'There is a new cancel request for delivery order with number ' .$this->deliveryNumber. '. Click here to go to details page.',
            'approval_id' => $this->approvalId
        ];
    }
}
