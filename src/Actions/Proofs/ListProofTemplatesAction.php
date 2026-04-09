<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Proofs;

use Proovit\LaravelProovit\Http\ProovitApiClient;

final class ListProofTemplatesAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(array $query = []): array
    {
        return $this->client->request('GET', '/v1/proof-templates', [
            'query' => $query,
        ]);
    }
}
