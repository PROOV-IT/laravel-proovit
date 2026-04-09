<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\Http\ProovitApiClient;

final class GetProofCertificateLinkAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {
    }

    public function handle(string $proofId): array
    {
        return $this->client->request('POST', "/v1/proofs/{$proofId}/certificate-link");
    }
}
