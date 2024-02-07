<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Auth;

class NotificationController extends Controller
{
    public function updateStatus($id)
    {
        $notification = Notification::find($id);

        if ($notification) {
            // Update the 'viewed' attribute to true
            $notification->update(['viewed' => true]);

            return response()->json(['message' => 'Notification status updated successfully']);
        }

        return response()->json(['error' => 'Notification not found'], 404);
    }
}
