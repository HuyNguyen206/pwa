<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PrecacheManifestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = File::exists($manifestPath) ?
            json_decode(File::get($manifestPath), true) : [];
        $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        $viteFiles = collect($manifest)
            ->pluck('file')
            ->filter()
            ->map(fn($file) => '/build/' . $file);

        $shell = [
            '/',
            '/offline',
            '/icons/field_logger_192.png',
            '/icons/field_logger_512.png',
            '/images/placeholder.svg'
        ];

        if (app()->environment('local')) {
//            $shell[] = 'https://pwa.test:5173/resources/css/app.css';
            $shell[] = 'https://pwa.test:5174/resources/js/app.js';
        }

        $urls = collect($shell)
            ->merge($viteFiles)
            ->unique()
            ->values();

        $versionSource = json_encode($manifest, $jsonFlags)
            . json_encode($urls, $jsonFlags);

        $etag = sha1($versionSource);
        $version = substr($etag, 0, 8);

        return response()->json(
            ['version' => $version, 'urls' => $urls],
            200,
            ['Cache-Controler' => 'no-cache, no-store, must-revalidate'],
            $jsonFlags
        )->setEtag($etag);
    }
}
