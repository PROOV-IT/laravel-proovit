<?php

declare(strict_types=1);

use Proovit\LaravelProovit\Support\ProovitCertificateResolver;

it('resolves a certificate payload into a dto', function (): void {
    $resolver = new ProovitCertificateResolver;

    $certificate = $resolver->resolve([
        'certificate' => [
            'url' => 'https://example.test/certificates/proof-123.pdf',
            'downloaded_at' => '2026-04-09T10:00:00Z',
        ],
    ], 'proof-123');

    expect($certificate->proofId)->toBe('proof-123')
        ->and($certificate->url)->toBe('https://example.test/certificates/proof-123.pdf')
        ->and($certificate->downloadedAt)->toBe('2026-04-09T10:00:00Z');
});

it('builds a downloadable filename for a certificate', function (): void {
    $resolver = new ProovitCertificateResolver;

    $certificate = $resolver->resolve([
        'proof_id' => 'proof-123',
    ]);

    expect($resolver->filename($certificate))->toBe('proof-123-certificate.pdf');
});
