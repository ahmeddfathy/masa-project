<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
  use HandlesAuthorization;

  public function viewAny(User $user): bool
  {
    return true;
  }

  public function view(User $user, Order $order): bool
  {
    return $user->id === $order->user_id || $user->hasRole('admin');
  }

  public function create(User $user): bool
  {
    return true;
  }

  public function update(User $user, Order $order): bool
  {
    \Log::info('Checking order update permission', [
        'user' => $user->toArray(),
        'has_admin_role' => $user->hasRole('admin')
    ]);
    return $user->hasRole('admin');
  }

  public function delete(User $user, Order $order): bool
  {
    return $user->hasRole('admin');
  }
}
