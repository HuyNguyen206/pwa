<?php

return [
    'id' => '/',
    'name' => env('APP_NAME', 'Laravel'),
    'short_name' => 'Logger',
    'description' => 'Records and review field meter reading',

    'icons' => [
        [
            'src' => '/icons/field_logger_192.png',
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any',
        ],
        [
            'src' => '/icons/field_logger_512.png',
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any',
        ],
    ],

    'screenshots' => [
        [
            'src' => '/screenshoots/mobile.png',
            'sizes' => '842x933',
            'type' => 'image/png',
        ],
        [
            'src' => '/screenshoots/desktop.png',
            'sizes' => '2525x1252',
            'type' => 'image/png',
            'form_factor' => 'wide',
        ],
    ],

    'start_url' => '/',
    'display' => 'standalone',
    'orientation' => 'portrait',
    'background_color' => '#030712',
    'theme_color' => '#030712',

    'categories' => [
        'utilities',
        'productivity',
    ]
];
