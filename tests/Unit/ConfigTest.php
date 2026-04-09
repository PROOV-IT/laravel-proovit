<?php

declare(strict_types=1);

use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\Enums\ProovitMode;
use Proovit\LaravelProovit\Support\ProovitFeatureManager;

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
        'features' => [
            'proofs' => true,
            'certificates' => false,
        ],
    ]);

    expect($config->baseUrl)->toBe('https://api.example.test');
    expect($config->appUrl)->toBe('https://app.example.test');
    expect($config->mode)->toBe(ProovitMode::Sandbox);
    expect($config->featureEnabled('proofs'))->toBeTrue();
    expect($config->featureEnabled('certificates'))->toBeFalse();
});

it('exposes the feature manager flags', function (): void {
    $config = ProovitConfig::fromArray([
        'connection' => [
            'base_url' => 'https://api.example.test',
        ],
        'features' => [
            'proofs' => true,
            'certificates' => true,
            'exports' => false,
            'audit' => true,
        ],
    ]);

    $manager = new ProovitFeatureManager($config);

    expect($manager->proofs())->toBeTrue()
        ->and($manager->certificates())->toBeTrue()
        ->and($manager->exports())->toBeFalse()
        ->and($manager->audit())->toBeTrue();
});
