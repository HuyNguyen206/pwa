<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrecacheManifestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $shell = [
            '/',
            '/offline',
            '/icons/field_logger_192.png',
            '/icons/field_logger_512.png',
            'https://pwa.test:5174/resources/js/app.js',
            'https://pwa.test:5174/resources/css/app.css',
        ];

        return response()->json(
            ['version' => 1, 'urls' => $shell],
            200,
            ['Cache-Controler' => 'no-cache, no-store, must-revalidate'],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
