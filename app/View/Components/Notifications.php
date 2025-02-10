<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
  public $notifications;
  public $unreadCount;

  public function __construct()
  {
    $user = Auth::user();
    $this->notifications = $user->notifications()->take(5)->get();
    $this->unreadCount = $user->unreadNotifications()->count();
  }

  public function render()
  {
    return view('components.notifications');
  }
}
