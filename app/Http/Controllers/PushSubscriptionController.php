<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
        ]);

        $request->user()->pushSubscriptions()->updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'p256dh' => $data['keys']['p256dh'],
                'auth' => $data['keys']['auth'],
            ]
        );

        return response()->json(['is_success' => true]);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        $request->user()->pushSubscriptions()->where('endpoint', $data['endpoint'])
            ->delete();

        return response()->json(['is_success' => true]);
    }
}
