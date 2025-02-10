<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $status = ucfirst($this->appointment->status);

        return (new MailMessage)
            ->subject("Appointment Status Updated: {$status}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your appointment scheduled for {$this->appointment->date->format('F j, Y')} at {$this->appointment->time} has been updated to {$status}.")
            ->when($this->appointment->notes, function ($message) {
                return $message->line("Notes: {$this->appointment->notes}");
            })
            ->action('View Appointment', route('appointments.show', $this->appointment))
            ->line('Thank you for choosing our services!');
    }
}
