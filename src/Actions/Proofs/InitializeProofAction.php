<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\Builders\Proofs\ProofBuilder;
use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\Events\Proofs\ProofInitialized;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class InitializeProofAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(array|ProofBuilder $payload): ProofData
    {
        $payload = $payload instanceof ProofBuilder ? $payload->toInitPayload() : $payload;

        $response = $this->client->request('POST', '/v1/proofs/init', [
            'json' => $payload,
        ]);

        $proof = ProofData::fromArray($response['proof'] ?? $response['data'] ?? $response);

        event(new ProofInitialized($proof, $payload, $response));

        return $proof;
    }
}
