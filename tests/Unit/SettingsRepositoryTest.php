<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\Support\ProovitConfigResolver;
use Proovit\LaravelProovit\Support\ProovitSettingsRepository;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Schema::dropIfExists('proovit_settings');

    Schema::create('proovit_settings', function (Blueprint $table): void {
        $table->id();
        $table->string('key')->unique();
        $table->longText('payload');
        $table->timestamps();
    });
});

it('persists settings and makes them override env defaults', function (): void {
    config()->set('proovit.connection.base_url', 'https://env.example.test');
    config()->set('proovit.connection.app_url', 'https://env-app.example.test');

    $repository = app(ProovitSettingsRepository::class);

    expect($repository->save([
        'connection' => [
            'base_url' => 'https://stored.example.test',
            'company_name' => 'Stored Company',
            'login_email' => 'admin@stored.example.test',
            'selected_company_uuid' => 'company-uuid',
            'companies' => [
                ['uuid' => 'company-uuid', 'name' => 'Stored Company'],
            ],
        ],
        'features' => [
            'proofs' => false,
        ],
    ]))->toBeTrue();

    $config = app(ProovitConfigResolver::class)->resolve();

    expect($config)->toBeInstanceOf(ProovitConfig::class);
    expect($config->baseUrl)->toBe('https://stored.example.test/api');
    expect($config->appUrl)->toBe('https://env-app.example.test');
    expect($config->companyName)->toBe('Stored Company');
    expect($config->loginEmail)->toBe('admin@stored.example.test');
    expect($config->selectedCompanyUuid)->toBe('company-uuid');
    expect($config->companies)->toHaveCount(1);
    expect($config->featureEnabled('proofs'))->toBeFalse();
});
