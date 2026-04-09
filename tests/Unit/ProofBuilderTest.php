<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Proovit\LaravelProovit\Actions\Proofs\InitializeProofAction;
use Proovit\LaravelProovit\Actions\Proofs\SignProofAction;
use Proovit\LaravelProovit\Actions\Proofs\UploadProofFilesAction;
use Proovit\LaravelProovit\Builders\Proofs\ProofBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofFilesBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofMetadataBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofSignatureBuilder;
use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\Http\ProovitApiClient;

it('builds an init payload from a fluent proof builder', function (): void {
    $history = [];
    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], json_encode([
            'proof' => [
                'id' => 'proof-1',
                'status' => 'init',
                'name' => 'test titre',
                'description' => 'azeazeaze',
            ],
        ], JSON_THROW_ON_ERROR)),
    ]));
    $stack->push(Middleware::history($history));

    $client = new ProovitApiClient(new Client([
        'handler' => $stack,
        'base_uri' => 'https://api.example.test/api/',
    ]));

    $builder = (new ProofBuilder)
        ->withName('test titre')
        ->withDescription('azeazeaze')
        ->withFolderId('folder-uuid')
        ->withCategoryId('category-uuid')
        ->withTokenReservationId('token-uuid')
        ->withProofTemplateId('template-uuid')
        ->withMetadata(static function (ProofMetadataBuilder $metadata): void {
            $metadata
                ->withShareEmails([])
                ->withAnonymous(false)
                ->withKeywords([])
                ->withLocation('430 route de la bonde, 84240 la tour d\'aigues', 43.7265, 5.5434)
                ->withCustomFields([
                    'material' => 'custom field 1 value required',
                    'quantity' => 2,
                    'delivery_date' => '2026-04-09',
                    'delivery_person' => 'custom field livreur value not required',
                ]);
        });

    $proof = (new InitializeProofAction($client))->handle($builder);

    $requestBody = json_decode((string) $history[0]['request']->getBody(), true, 512, JSON_THROW_ON_ERROR);

    expect($proof)->toBeInstanceOf(ProofData::class)
        ->and($proof->id)->toBe('proof-1')
        ->and($requestBody)->toMatchArray([
            'name' => 'test titre',
            'description' => 'azeazeaze',
            'folder_id' => 'folder-uuid',
            'category_id' => 'category-uuid',
            'token_reservation_id' => 'token-uuid',
            'proof_template_id' => 'template-uuid',
            'metadata' => [
                'share_emails' => [],
                'is_anonymous' => false,
                'keywords' => [],
                'location' => '430 route de la bonde, 84240 la tour d\'aigues',
                'lat' => 43.7265,
                'lng' => 5.5434,
                'custom_fields' => [
                    'material' => 'custom field 1 value required',
                    'quantity' => 2,
                    'delivery_date' => '2026-04-09',
                    'delivery_person' => 'custom field livreur value not required',
                ],
            ],
        ]);
});

it('builds multipart file payloads with hashes and total bytes from a fluent proof builder', function (): void {
    $history = [];
    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], json_encode(['ok' => true], JSON_THROW_ON_ERROR)),
    ]));
    $stack->push(Middleware::history($history));

    $client = new ProovitApiClient(new Client([
        'handler' => $stack,
        'base_uri' => 'https://api.example.test/api/',
    ]));

    $builder = (new ProofBuilder)
        ->withFiles(static function (ProofFilesBuilder $files): void {
            $files->addFileFromContents('hello world', 'document.pdf', 'application/pdf');
        });

    (new UploadProofFilesAction($client))->handle('proof-1', $builder);

    $capturedMultipart = (string) $history[0]['request']->getBody();

    expect($capturedMultipart)
        ->toContain('hello world')
        ->toContain('document.pdf')
        ->toContain(hash('sha256', 'hello world'))
        ->toContain('client_total_bytes')
        ->toContain((string) strlen('hello world'));
});

it('builds a sign payload from a fluent proof builder', function (): void {
    $history = [];
    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], json_encode([
            'ok' => true,
        ], JSON_THROW_ON_ERROR)),
    ]));
    $stack->push(Middleware::history($history));

    $client = new ProovitApiClient(new Client([
        'handler' => $stack,
        'base_uri' => 'https://api.example.test/api/',
    ]));

    $builder = (new ProofBuilder)
        ->withSignature(static function (ProofSignatureBuilder $signature): void {
            $signature
                ->withSignatureBase64('data:image/png;base64,abc123')
                ->withClientContext([
                    'userAgent' => 'Mozilla/5.0',
                    'language' => 'fr-FR',
                    'timeZone' => 'Europe/Paris',
                ]);
        });

    (new SignProofAction($client))->handle('proof-1', $builder);

    $requestBody = json_decode((string) $history[0]['request']->getBody(), true, 512, JSON_THROW_ON_ERROR);

    expect($requestBody)->toMatchArray([
        'signature_base64' => 'data:image/png;base64,abc123',
        'client_context' => [
            'userAgent' => 'Mozilla/5.0',
            'language' => 'fr-FR',
            'timeZone' => 'Europe/Paris',
        ],
    ]);
});

it('refuses to sign a proof builder without a signature payload', function (): void {
    $client = new ProovitApiClient(new Client([
        'handler' => HandlerStack::create(new MockHandler([
            new Response(200, [], json_encode(['ok' => true], JSON_THROW_ON_ERROR)),
        ])),
        'base_uri' => 'https://api.example.test/api/',
    ]));

    (new SignProofAction($client))->handle('proof-1', (new ProofBuilder)->withSignature());
})->throws(InvalidArgumentException::class, 'A proof signature base64 value is required before signing.');
