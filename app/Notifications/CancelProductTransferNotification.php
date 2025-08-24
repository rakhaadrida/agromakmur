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
            'subject' => 'Cancel Product Transfer Request - ' . $this->transferNumber,
            'message' => 'There is a new cancel request for product transfer with number ' .$this->transferNumber. '. Click here to go to details page.',
            'approval_id' => $this->approvalId
        ];
    }
}
