<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentConfirmed extends Notification
{
  use Queueable;

  protected $appointment;

  public function __construct(Appointment $appointment)
  {
    $this->appointment = $appointment;
  }

  public function via($notifiable): array
  {
    return ['mail', 'database'];
  }

  public function toMail($notifiable): MailMessage
  {
    return (new MailMessage)
      ->subject('Appointment Confirmed')
      ->line('Your appointment has been confirmed!')
      ->line('Date: ' . $this->appointment->date->format('F j, Y'))
      ->line('Time: ' . $this->appointment->time)
      ->line('Service: ' . ucfirst($this->appointment->service_type))
      ->action('View Appointment', route('appointments.show', $this->appointment))
      ->line('Thank you for choosing our services!');
  }

  public function toArray($notifiable): array
  {
    return [
      'title' => 'Appointment Confirmed',
      'message' => 'Your appointment on ' . $this->appointment->date->format('F j, Y') . ' at ' . $this->appointment->time . ' has been confirmed.',
      'type' => 'appointment_confirmed',
      'appointment_id' => $this->appointment->id
    ];
  }
}
