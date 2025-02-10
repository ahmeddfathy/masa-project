<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class NotificationPopup extends Component
{
  public $notification;

  public function __construct($notification)
  {
    $this->notification = $notification;
  }

  public function render()
  {
    return view('components.notification-popup');
  }
}
