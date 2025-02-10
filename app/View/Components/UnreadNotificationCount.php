<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class UnreadNotificationCount extends Component
{
  public $count;

  public function __construct()
  {
    $user = Auth::user();
    $this->count = $user->unreadNotifications->count();
  }

  public function render()
  {
    return view('components.unread-notification-count');
  }
}
