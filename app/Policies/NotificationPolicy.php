<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
  public function view(User $user, DatabaseNotification $notification): bool
  {
    return $notification->notifiable_id === $user->id;
  }

  public function update(User $user, DatabaseNotification $notification): bool
  {
    return $notification->notifiable_id === $user->id;
  }
}
