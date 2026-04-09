<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\Events\Proofs\ProofRevoked;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class RevokeProofAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(string $proofId, ?string $reason = null): array
    {
        $payload = array_filter([
            'reason' => $reason,
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        $response = $this->client->request('POST', "/v1/proofs/{$proofId}/revoke", [
            'json' => $payload,
        ]);

        $proof = ProofData::fromArray($response['proof'] ?? $response['data'] ?? ['id' => $proofId, 'status' => 'revoked']);
        event(new ProofRevoked($proofId, $proof, $payload, $response));

        return $response;
    }
}
