<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Categories;

use Proovit\LaravelProovit\Http\ProovitApiClient;

final class ListCategoriesAction
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
            'include_shared' => true,
        ], $query);

        return $this->client->request('GET', '/v1/categories', [
            'query' => $query,
        ]);
    }
}
