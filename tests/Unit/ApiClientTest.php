<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use Proovit\LaravelProovit\Exceptions\ApiException;
use Proovit\LaravelProovit\Http\ProovitApiClient;

it('throws an api exception for an error response', function (): void {
    $handler = HandlerStack::create(new MockHandler([
        new Response(422, [], json_encode(['message' => 'Invalid proof payload'], JSON_THROW_ON_ERROR)),
    ]));

    $client = new ProovitApiClient(new Client(['handler' => $handler]));

    $client->request('POST', '/v1/proofs/init', ['json' => ['name' => 'Test']]);
})->throws(ApiException::class, 'API error: 422 - Invalid proof payload');

it('returns an empty array for a successful empty response', function (): void {
    $handler = HandlerStack::create(new MockHandler([
        new Response(204, [], ''),
    ]));

    $client = new ProovitApiClient(new Client(['handler' => $handler]));

    expect($client->request('DELETE', '/v1/proofs/proof-123'))->toBe([]);
});

it('normalizes leading slashes so the base uri path is preserved', function (): void {
    $capturedUri = null;
    $handler = function ($request, $options) use (&$capturedUri) {
        $capturedUri = (string) $request->getUri();

        return Create::promiseFor(new Response(200, [], json_encode(['ok' => true], JSON_THROW_ON_ERROR)));
    };

    $client = new ProovitApiClient(new Client([
        'handler' => $handler,
        'base_uri' => 'https://api.example.test/api/',
    ]));

    expect($client->request('POST', '/v1/auth/login', [
        'json' => ['email' => 'admin@example.test', 'password' => 'secret'],
    ]))->toBe(['ok' => true]);

    expect($capturedUri)->toBe('https://api.example.test/api/v1/auth/login');
});
