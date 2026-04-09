<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Proovit\LaravelProovit\Actions\Connection\AuthenticateProovitConnectionAction;
use Proovit\LaravelProovit\Actions\Proofs\InitializeProofAction;
use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\Events\Connection\ConnectionAuthenticated;
use Proovit\LaravelProovit\Events\Proofs\ProofInitialized;
use Proovit\LaravelProovit\Http\ProovitApiClient;

it('dispatches an event after authenticating a connection', function (): void {
    Event::fake([ConnectionAuthenticated::class]);

    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], json_encode([
            'token' => 'bearer-token',
        ], JSON_THROW_ON_ERROR)),
        new Response(200, [], json_encode([
            'data' => [
                ['uuid' => 'company-uuid', 'name' => 'Company'],
            ],
        ], JSON_THROW_ON_ERROR)),
    ]));

    $client = new ProovitApiClient(new Client([
        'handler' => $stack,
        'base_uri' => 'https://api.example.test/api/',
    ]));

    $action = new AuthenticateProovitConnectionAction(
        ProovitConfig::fromArray([
            'connection' => [
                'base_url' => 'https://api.example.test',
            ],
        ]),
        static fn (ProovitConfig $config) => new Client([
            'handler' => $stack,
            'base_uri' => 'https://api.example.test/api/',
        ]),
    );

    $connection = $action->handle('admin@example.test', 'secret');

    expect($connection->bearerToken)->toBe('bearer-token');

    Event::assertDispatched(ConnectionAuthenticated::class, static function (ConnectionAuthenticated $event): bool {
        return $event->connection->bearerToken === 'bearer-token'
            && $event->connection->loginEmail === 'admin@example.test';
    });
});

it('dispatches an event after initializing a proof', function (): void {
    Event::fake([ProofInitialized::class]);

    $history = [];
    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], json_encode([
            'proof' => [
                'id' => 'proof-1',
                'status' => 'init',
                'name' => 'Example proof',
            ],
        ], JSON_THROW_ON_ERROR)),
    ]));
    $stack->push(Middleware::history($history));

    $client = new ProovitApiClient(new Client([
        'handler' => $stack,
        'base_uri' => 'https://api.example.test/api/',
    ]));

    $proof = (new InitializeProofAction($client))->handle([
        'name' => 'Example proof',
        'proof_template_id' => 'template-uuid',
    ]);

    expect($proof->id)->toBe('proof-1');

    Event::assertDispatched(ProofInitialized::class, static function (ProofInitialized $event): bool {
        return $event->proof->id === 'proof-1'
            && $event->payload['name'] === 'Example proof';
    });
});
