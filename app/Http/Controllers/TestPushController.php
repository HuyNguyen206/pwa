<?php

namespace App\Http\Controllers;

use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class TestPushController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, PushNotificationService $notificationService)
    {
        $user = $request->user();

        if (!$user->pushSubscriptions()->exists()) {
            return response()->json([
                'message' => 'No push subscriptions found for the user.',
                'status' => 'no-subscriptions'
            ], 404);
        }

        $results = $notificationService->sendToUser(
            $user,
            [
                'title' => 'Test Notification from BE',
                'body' => 'This is a test notification from BE.',
                'url' => route('meters.create'),
            ]
        );

        return response()->json(['results' => $results]);
    }
}
