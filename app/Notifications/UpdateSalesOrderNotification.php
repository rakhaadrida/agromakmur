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
            'subject' => 'Update Sales Order Request - ' . $this->orderNumber,
            'message' => 'There is a new update request for sales order with number ' .$this->orderNumber. '. Click the order number to redirect to the details page.',
            'approval_id' => $this->approvalId
        ];
    }
}
