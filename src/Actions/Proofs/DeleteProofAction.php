<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\Events\Proofs\ProofDeleted;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class DeleteProofAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(string $proofId): bool
    {
        $response = $this->client->request('DELETE', "/v1/proofs/{$proofId}");
        event(new ProofDeleted($proofId, true, $response));

        return true;
    }
}
