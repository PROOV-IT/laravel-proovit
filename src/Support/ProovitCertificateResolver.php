<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Support;

use Proovit\LaravelProovit\DTOs\ProofCertificateData;

final class ProovitCertificateResolver
{
    public function resolve(array $payload, ?string $proofId = null): ProofCertificateData
    {
        $certificate = $payload['certificate'] ?? $payload['data'] ?? $payload;

        if (! is_array($certificate)) {
            $certificate = [];
        }

        if ($proofId !== null && $proofId !== '' && ! isset($certificate['proof_id']) && ! isset($certificate['id'])) {
            $certificate['proof_id'] = $proofId;
        }

        return ProofCertificateData::fromArray($certificate);
    }

    public function filename(ProofCertificateData $certificate): string
    {
        $prefix = $certificate->proofId !== '' ? $certificate->proofId : 'proof';

        return sprintf('%s-certificate.pdf', $prefix);
    }
}
