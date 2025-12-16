<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications marquées comme lues.');
    }

    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Toutes les notifications ont été supprimées.');
    }

    // API Methods
    public function apiIndex(Request $request)
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->take($request->get('limit', 10))
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'type' => $notification->data['type'] ?? 'info',
                    'icon' => $notification->data['icon'] ?? 'fa-bell',
                    'read' => $notification->read_at !== null,
                    'created_at' => $notification->created_at->toISOString(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            });

        $unreadCount = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }

    public function apiMarkAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        
        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue'
        ]);
    }

    public function apiMarkAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications marquées comme lues'
        ]);
    }

    public function types()
    {
        $types = [
            'info' => ['name' => 'Information', 'color' => 'blue', 'icon' => 'fa-info-circle'],
            'success' => ['name' => 'Succès', 'color' => 'green', 'icon' => 'fa-check-circle'],
            'warning' => ['name' => 'Avertissement', 'color' => 'yellow', 'icon' => 'fa-exclamation-triangle'],
            'error' => ['name' => 'Erreur', 'color' => 'red', 'icon' => 'fa-times-circle'],
            'transaction' => ['name' => 'Transaction', 'color' => 'indigo', 'icon' => 'fa-exchange-alt'],
            'budget' => ['name' => 'Budget', 'color' => 'purple', 'icon' => 'fa-chart-pie'],
            'goal' => ['name' => 'Objectif', 'color' => 'pink', 'icon' => 'fa-bullseye'],
            'reminder' => ['name' => 'Rappel', 'color' => 'teal', 'icon' => 'fa-calendar'],
        ];

        return response()->json([
            'success' => true,
            'types' => $types
        ]);
    }

    public function test(Request $request)
    {
        $user = Auth::user();
        
        $user->notify(new \App\Notifications\TestNotification());

        return response()->json([
            'success' => true,
            'message' => 'Notification de test envoyée'
        ]);
    }

    public function websocketConnect(Request $request)
    {
        // Pour WebSocket/Pusher
        $pusher = new \Pusher\Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $socketId = $request->input('socket_id');
        $channelName = 'private-user-' . Auth::id();

        $auth = $pusher->socket_auth($channelName, $socketId);

        return response($auth);
    }

    public function websocketBroadcast(Request $request)
    {
        $request->validate([
            'channel' => 'required|string',
            'event' => 'required|string',
            'data' => 'required|array'
        ]);

        $pusher = new \Pusher\Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $pusher->trigger(
            $request->channel,
            $request->event,
            $request->data
        );

        return response()->json(['success' => true]);
    }
}