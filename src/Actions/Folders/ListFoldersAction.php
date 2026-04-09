<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Folders;

use Proovit\LaravelProovit\Http\ProovitApiClient;

final class ListFoldersAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(array $query = []): array
    {
        $query = array_replace([
            'per_page' => 200,
            'accessible_by' => 'write',
        ], $query);

        return $this->client->request('GET', '/v1/folders', [
            'query' => $query,
        ]);
    }
}
