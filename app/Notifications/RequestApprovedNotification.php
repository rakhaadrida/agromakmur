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
            'subject' => 'Request Approved - ' . $this->transactionNumber,
            'message' => 'Change request for ' . $this->type . ' with number ' . $this->transactionNumber . ' has been Approved. Click here to go to details page.',
            'approval_id' => $this->approvalId
        ];
    }
}
