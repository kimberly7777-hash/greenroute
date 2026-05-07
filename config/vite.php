<?php

use Illuminate\Foundation\Vite\ViteServiceProvider;

return [
    // The public path where compiled Vite assets are written.
    // Laravel's @vite() / Vite::asset() relies on the manifest.json at this location.
    'manifest_path' => public_path('build/manifest.json'),

    // The URL where the Vite dev server is running.
    // When APP_ENV=local and a dev server is reachable, Laravel will prefer dev-server assets.
    'dev_server_url' => env('VITE_URL', env('VITE_DEV_SERVER_URL', 'http://localhost:5173')),

    // The input files used by Vite (Laravel uses this only for the dev-server entrypoint).
    'inputs' => [
        'resources/css/app.css',
        'resources/js/app.js',
    ],
];

