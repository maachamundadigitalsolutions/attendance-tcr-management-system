<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // List unread notifications for authenticated user
    public function index(Request $request)
    {
        $user = $request->user(); // Sanctum will resolve user from Bearer token

        return response()->json([
            'notifications' => $user->unreadNotifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? 'Notification',
                    'message' => $n->data['message'] ?? '',
                    'created_at' => $n->created_at->toDateTimeString(),
                ];
            })
        ]);
    }

    // Mark a notification as read
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user(); // Sanctum resolves user from token
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'not_found'], 404);
    }
}
