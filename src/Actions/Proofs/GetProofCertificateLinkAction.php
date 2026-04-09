<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\DTOs\ProofCertificateData;
use Proovit\LaravelProovit\Http\ProovitApiClient;
use Proovit\LaravelProovit\Support\ProovitCertificateResolver;

final class GetProofCertificateLinkAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
        private readonly ProovitCertificateResolver $resolver = new ProovitCertificateResolver,
    ) {}

    public function handle(string $proofId): ProofCertificateData
    {
        $response = $this->client->request('POST', "/v1/proofs/{$proofId}/certificate-link");

        return $this->resolver->resolve($response, $proofId);
    }
}
