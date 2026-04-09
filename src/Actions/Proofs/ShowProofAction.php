<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class ShowProofAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(string $proofId): ProofData
    {
        $response = $this->client->request('GET', "/v1/proofs/{$proofId}");

        return ProofData::fromArray($response['proof'] ?? $response['data'] ?? $response);
    }
}
