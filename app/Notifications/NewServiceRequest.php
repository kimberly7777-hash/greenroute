<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\Contractor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewServiceRequest extends Notification
{
    use Queueable;

    protected $request;
    protected $contractor;

    public function __construct(ServiceRequest $request, Contractor $contractor)
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
        $url = url('/dashboard/contractor/clients/requests');

        return (new MailMessage)
            ->subject('New Service Request from ' . $this->request->name)
            ->greeting('Hello ' . ($this->contractor->company_name ?? 'Contractor'))
            ->line('A new service request has been submitted by ' . $this->request->name . '.')
            ->line('Phone: ' . $this->request->phone)
            ->line('Email: ' . ($this->request->email ?? 'N/A'))
            ->line('Address: ' . ($this->request->address ?? 'N/A'))
            ->action('Review Requests', $url)
            ->line('You can accept to create the client account, or reject with a reason.');
    }
}
