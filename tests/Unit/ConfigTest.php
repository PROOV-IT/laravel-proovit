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
            'selected_company_uuid' => 'company-uuid',
            'workspace_token' => 'workspace',
            'companies' => [['uuid' => 'company-uuid', 'name' => 'Company']],
            'mode' => 'sandbox',
        ],
        'features' => [
            'proofs' => true,
            'certificates' => false,
        ],
    ]);

    expect($config->baseUrl)->toBe('https://api.example.test/api');
    expect($config->appUrl)->toBe('https://app.example.test');
    expect($config->selectedCompanyUuid)->toBe('company-uuid');
    expect($config->companyName)->toBeNull();
    expect($config->loginEmail)->toBeNull();
    expect($config->companies)->toHaveCount(1);
    expect($config->mode)->toBe(ProovitMode::Sandbox);
    expect($config->headers())->toMatchArray([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer token',
        'X-COMPANY-TOKEN' => 'company-uuid',
        'X-COMPANY-ID' => 'company-uuid',
    ]);
    expect($config->toArray()['connection']['selected_company_uuid'])->toBe('company-uuid');
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
