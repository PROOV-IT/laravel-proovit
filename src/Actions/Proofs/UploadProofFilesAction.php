<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\Http\ProovitApiClient;
use Proovit\LaravelProovit\Support\ProovitPayloadNormalizer;

final class UploadProofFilesAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
        private readonly ProovitPayloadNormalizer $normalizer = new ProovitPayloadNormalizer,
    ) {}

    public function handle(string $proofId, array $files): array
    {
        $response = $this->client->request('POST', "/v1/proofs/{$proofId}/files", [
            'multipart' => $this->normalizer->normalizeFiles($files),
        ]);

        return $response;
    }
}
