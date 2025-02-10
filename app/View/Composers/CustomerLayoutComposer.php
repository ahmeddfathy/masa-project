<?php

namespace App\View\Composers;

use App\Models\CartItem;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CustomerLayoutComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $user = Auth::user();
        if (!$user) {
            $view->with([
                'stats' => [
                    'cart_items_count' => 0,
                    'unread_notifications' => 0,
                ],
                'recent_notifications' => collect([]),
            ]);
            return;
        }

        // إحصائيات العميل
        $cartItemsCount = CartItem::join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('carts.user_id', $user->id)
            ->sum('cart_items.quantity');

        $stats = [
            'cart_items_count' => $cartItemsCount,
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        // آخر الإشعارات
        $recent_notifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                $notification->icon = $this->getNotificationIcon($notification->type);
                return $notification;
            });

        $view->with(compact('stats', 'recent_notifications'));
    }

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon($type): string
    {
        return match ($type) {
            'App\Notifications\OrderStatusChanged' => 'fa-shopping-bag',
            'App\Notifications\AppointmentConfirmed' => 'fa-calendar-check',
            'App\Notifications\AppointmentCancelled' => 'fa-calendar-times',
            'App\Notifications\NewOrder' => 'fa-shopping-cart',
            'App\Notifications\PaymentReceived' => 'fa-credit-card',
            default => 'fa-bell'
        };
    }
}
