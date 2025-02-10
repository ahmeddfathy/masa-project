<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
  use HandlesAuthorization;

  public function viewDashboard(User $user): bool
  {
    return $user->hasRole('admin');
  }
}
