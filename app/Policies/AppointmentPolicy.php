<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->user_id || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->user_id || $user->hasRole('admin');
    }
}
