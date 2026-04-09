<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\Http\ProovitApiClient;

final class DeleteProofAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(string $proofId): bool
    {
        $this->client->request('DELETE', "/v1/proofs/{$proofId}");

        return true;
    }
}
