<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\Http\ProovitApiClient;

final class DownloadProofCertificateAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(string $proofId): string
    {
        return $this->client->download("/v1/proofs/{$proofId}/certificate.pdf");
    }
}
