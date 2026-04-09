<?php

declare(strict_types=1);

use Proovit\LaravelProovit\DTOs\ProofData;

it('falls back to the certificate link when the direct certificate url is missing', function (): void {
    $proof = ProofData::fromArray([
        'id' => 'proof-uuid',
        'status' => 'signed',
        'links' => [
            'certificate_pdf' => 'https://api.example.test/t/certificate',
        ],
    ]);

    expect($proof->certificateUrl)->toBe('https://api.example.test/t/certificate');
});
