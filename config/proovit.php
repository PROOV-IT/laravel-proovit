<?php

return [
    'connection' => [
        'base_url' => env('PROOVIT_BASE_URL', 'https://api.proov-it.online'),
        'app_url' => env('PROOVIT_APP_URL', 'https://proov-it.io'),
        'api_key' => env('PROOVIT_API_KEY'),
        'access_token' => env('PROOVIT_ACCESS_TOKEN'),
        'workspace_token' => env('PROOVIT_WORKSPACE_TOKEN'),
        'mode' => env('PROOVIT_MODE', 'production'),
        'timeout' => (int) env('PROOVIT_TIMEOUT', 30),
        'connect_timeout' => (int) env('PROOVIT_CONNECT_TIMEOUT', 10),
        'verify_tls' => env('PROOVIT_VERIFY_TLS', true),
        'retry_attempts' => (int) env('PROOVIT_RETRY_ATTEMPTS', 0),
        'retry_sleep_ms' => (int) env('PROOVIT_RETRY_SLEEP_MS', 250),
        'health_endpoint' => env('PROOVIT_HEALTH_ENDPOINT', '/v1/health'),
    ],

    'features' => [
        'proofs' => true,
        'certificates' => true,
        'exports' => true,
        'audit' => true,
    ],

    'docs' => [
        'enabled' => env('PROOVIT_DOCS_ENABLED', false),
        'path' => env('PROOVIT_DOCS_PATH', 'docs/api/proovit'),
    ],
];
