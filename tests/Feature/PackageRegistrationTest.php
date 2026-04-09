<?php

declare(strict_types=1);

use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\ProovitClient;

it('registers the package configuration and client', function (): void {
    expect(config('proovit.connection.base_url'))->toBeString();

    $config = app(ProovitConfig::class);
    expect($config)->toBeInstanceOf(ProovitConfig::class);

    $client = app(ProovitClient::class);
    expect($client)->toBeInstanceOf(ProovitClient::class);
});
