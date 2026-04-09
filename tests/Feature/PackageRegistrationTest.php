<?php

declare(strict_types=1);

use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\ProovitClient;
use Proovit\LaravelProovit\Support\ProovitConfigResolver;
use Proovit\LaravelProovit\Support\ProovitFeatureManager;
use Proovit\LaravelProovit\Support\ProovitSettingsRepository;

it('registers the package configuration and client', function (): void {
    expect(config('proovit.connection.base_url'))->toBeString();

    $config = app(ProovitConfig::class);
    expect($config)->toBeInstanceOf(ProovitConfig::class);

    $client = app(ProovitClient::class);
    expect($client)->toBeInstanceOf(ProovitClient::class);

    expect(app(ProovitConfigResolver::class))->toBeInstanceOf(ProovitConfigResolver::class);
    expect(app(ProovitFeatureManager::class))->toBeInstanceOf(ProovitFeatureManager::class);
    expect(app(ProovitSettingsRepository::class))->toBeInstanceOf(ProovitSettingsRepository::class);
});
