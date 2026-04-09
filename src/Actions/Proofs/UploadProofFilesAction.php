<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\Builders\Proofs\ProofBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofFilesBuilder;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class UploadProofFilesAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(string $proofId, array|ProofBuilder|ProofFilesBuilder $files): array
    {
        if ($files instanceof ProofBuilder) {
            $files = $files->files();
        }

        if (is_array($files)) {
            $files = ProofFilesBuilder::fromLegacyFiles($files);
        }

        $response = $this->client->request('POST', "/v1/proofs/{$proofId}/files", [
            'multipart' => $files->toMultipart(),
        ]);

        return $response;
    }
}
