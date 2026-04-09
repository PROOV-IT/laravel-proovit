<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class InitializeProofAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(array $payload): ProofData
    {
        $response = $this->client->request('POST', '/v1/proofs/init', [
            'json' => $payload,
        ]);

        return ProofData::fromArray($response['proof'] ?? $response['data'] ?? $response);
    }
}
