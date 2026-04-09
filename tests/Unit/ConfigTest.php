<?php

declare(strict_types=1);

use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\Enums\ProovitMode;

it('builds a config object from array', function (): void {
    $config = ProovitConfig::fromArray([
        'connection' => [
            'base_url' => 'https://api.example.test',
            'app_url' => 'https://app.example.test',
            'api_key' => 'secret',
            'access_token' => 'token',
            'workspace_token' => 'workspace',
            'mode' => 'sandbox',
        ],
    ]);

    expect($config->baseUrl)->toBe('https://api.example.test');
    expect($config->appUrl)->toBe('https://app.example.test');
    expect($config->mode)->toBe(ProovitMode::Sandbox);
});
