<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class NotificationList extends Component
{
  public $notifications;
  public $showAll;

  public function __construct($showAll = false)
  {
    $user = Auth::user();
    $query = $user->notifications();

    if (!$showAll) {
      $query->take(5);
    }

    $this->notifications = $query->latest()->get();
    $this->showAll = $showAll;
  }

  public function render()
  {
    return view('components.notification-list');
  }
}
