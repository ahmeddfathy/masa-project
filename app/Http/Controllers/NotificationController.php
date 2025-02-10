<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();
        return back()->with('success', 'تم تحديد الإشعار كمقروء');
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');
    }
}
