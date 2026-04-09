<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

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

        return $this->client->request('POST', "/v1/proofs/{$proofId}/revoke", [
            'json' => $payload,
        ]);
    }
}
