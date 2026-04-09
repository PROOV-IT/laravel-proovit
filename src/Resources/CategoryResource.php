<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Resources;

use Proovit\LaravelProovit\Actions\Categories\ListCategoriesAction;
use Proovit\LaravelProovit\DTOs\CategoryData;

final class CategoryResource
{
    public function __construct(
        private readonly ListCategoriesAction $listAction,
    ) {}

    /**
     * @return array<int, CategoryData>
     */
    public function all(array $query = []): array
    {
        $response = $this->listAction->handle($query);
        $categories = $response['data'] ?? $response['items'] ?? $response['categories'] ?? [];

        return array_values(array_filter(array_map(
            static fn (array $category): CategoryData => CategoryData::fromArray($category),
            is_array($categories) ? $categories : [],
        )));
    }

    /**
     * @return array<string, string>
     */
    public function options(array $query = []): array
    {
        return collect($this->all($query))
            ->filter(static fn (CategoryData $category): bool => $category->isActive)
            ->sortBy('name')
            ->mapWithKeys(static fn (CategoryData $category): array => [
                $category->id => $category->name,
            ])
            ->all();
    }
}
