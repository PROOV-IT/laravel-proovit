<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use InvalidArgumentException;
use Proovit\LaravelProovit\Builders\Proofs\ProofBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofSignatureBuilder;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class SignProofAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(string $proofId, string|ProofBuilder|ProofSignatureBuilder|null $signatureBase64 = null, array $clientContext = []): array
    {
        if ($signatureBase64 instanceof ProofBuilder) {
            $signatureBase64 = $signatureBase64->signature();
        }

        if ($signatureBase64 instanceof ProofSignatureBuilder) {
            $payload = $signatureBase64->toArray();
            if ($payload === [] || ! array_key_exists('signature_base64', $payload)) {
                throw new InvalidArgumentException('A proof signature base64 value is required before signing.');
            }
        } else {
            $payload = array_filter([
                'signature_base64' => $signatureBase64,
                'client_context' => $clientContext !== [] ? $clientContext : null,
            ], static fn ($value): bool => $value !== null);
        }

        return $this->client->request('POST', "/v1/proofs/{$proofId}/sign", [
            'json' => $payload,
        ]);
    }
}
