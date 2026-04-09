<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Resources;

use Proovit\LaravelProovit\Actions\Folders\ListFoldersAction;
use Proovit\LaravelProovit\DTOs\FolderData;

final class FolderResource
{
    public function __construct(
        private readonly ListFoldersAction $listAction,
    ) {}

    /**
     * @return array<int, FolderData>
     */
    public function all(array $query = []): array
    {
        $response = $this->listAction->handle($query);
        $folders = $response['data'] ?? $response['items'] ?? $response['folders'] ?? [];

        return array_values(array_filter(array_map(
            static fn (array $folder): FolderData => FolderData::fromArray($folder),
            is_array($folders) ? $folders : [],
        )));
    }

    /**
     * @return array<string, string>
     */
    public function options(array $query = []): array
    {
        return collect($this->all($query))
            ->filter(static fn (FolderData $folder): bool => $folder->isActive)
            ->sortBy('name')
            ->mapWithKeys(static fn (FolderData $folder): array => [
                $folder->id => $folder->name,
            ])
            ->all();
    }
}
