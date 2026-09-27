<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubcriptionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string'],
            'keys.p256dn' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
        ]);

        $request->user()->pushSubscriptions()->updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'p256dn' => $data['keys']['p256dn'],
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
