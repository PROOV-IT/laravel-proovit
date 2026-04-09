<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Proovit\LaravelProovit\Actions\Connection\AuthenticateProovitConnectionAction;
use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\Enums\ProovitMode;

it('authenticates against the api and loads companies', function (): void {
    $handler = HandlerStack::create(new MockHandler([
        new Response(201, [], json_encode(['token' => 'bearer-token'], JSON_THROW_ON_ERROR)),
        new Response(200, [], json_encode([
            'data' => [
                [
                    'uuid' => 'company-uuid',
                    'name' => 'Company Name',
                ],
            ],
        ], JSON_THROW_ON_ERROR)),
    ]));

    $httpClient = new Client(['handler' => $handler]);

    $config = new ProovitConfig(
        baseUrl: 'https://api.example.test/api',
        mode: ProovitMode::Production,
    );

    $action = new AuthenticateProovitConnectionAction(
        $config,
        static fn () => $httpClient,
    );

    $connection = $action->handle('admin@example.test', 'secret');

    expect($connection->connected)->toBeTrue()
        ->and($connection->bearerToken)->toBe('bearer-token')
        ->and($connection->loginEmail)->toBe('admin@example.test')
        ->and($connection->companies)->toHaveCount(1)
        ->and($connection->companies[0]['uuid'])->toBe('company-uuid');
});
