<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestRejected extends Notification
{
    use Queueable;

    protected $request;
    protected $contractor;

    public function __construct(ServiceRequest $request, $contractor)
    {
        $this->request = $request;
        $this->contractor = $contractor;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $contactPhone = $this->contractor->phone ?? 'N/A';
        $contactEmail = $this->contractor->email ?? 'N/A';

        return (new MailMessage)
            ->subject('Service Request Rejected')
            ->greeting('Hello ' . $this->request->name)
            ->line('We are sorry to inform you that your service request has been rejected by ' . ($this->contractor->company_name ?? 'your contractor') . '.')
            ->line('Reason: ' . ($this->request->rejection_reason ?? 'No reason provided.'))
            ->line('For more help, contact your contractor at:')
            ->line('Phone: ' . $contactPhone)
            ->line('Email: ' . $contactEmail)
            ->line('If you need further assistance, please reply to this email.');
    }
}
