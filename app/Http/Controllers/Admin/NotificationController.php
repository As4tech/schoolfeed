<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function unread()
    {
        $notifications = auth()->user()->unreadNotifications()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(DatabaseNotification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }

    public function show(DatabaseNotification $notification)
    {
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        $data = $notification->data;
        $actionUrl = $data['action_url'] ?? null;

        if ($actionUrl) {
            return redirect($actionUrl);
        }

        return view('admin.notifications.show', compact('notification'));
    }
}
