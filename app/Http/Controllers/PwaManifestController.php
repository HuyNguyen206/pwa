<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;

class PwaManifestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $pwa = Config::get('pwa');

        $manifest = [
            'name' => $pwa['name'],
            'short_name' => $pwa['short_name'],
            'description' => $pwa['description'],
            'start_url' => $pwa['start_url'],
            'id' => $pwa['id'],
            'display' => $pwa['display'],
            'orientation' => $pwa['orientation'],
            'background_color' => $pwa['background_color'],
            'theme_color' => $pwa['theme_color'],
            'icons' => $pwa['icons'],
            'screenshots' => $pwa['screenshots'],
            'categories' => $pwa['categories'],
        ];

        $json = json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return Response::make($json, headers: [
            'Content-Type' => 'application/manifest+json',
            'Cache-Control' => 'public, max-age=0, must-revalidate',
            // 'Cache-Control' => 'public, max-age=86400' // 1 day
        ]);
    }
}
