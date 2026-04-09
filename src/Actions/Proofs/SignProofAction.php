<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\Http\ProovitApiClient;

final class SignProofAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(string $proofId, ?string $signatureBase64 = null, array $clientContext = []): array
    {
        $payload = array_filter([
            'signature_base64' => $signatureBase64,
            'client_context' => $clientContext !== [] ? $clientContext : null,
        ], static fn ($value): bool => $value !== null);

        return $this->client->request('POST', "/v1/proofs/{$proofId}/sign", [
            'json' => $payload,
        ]);
    }
}
