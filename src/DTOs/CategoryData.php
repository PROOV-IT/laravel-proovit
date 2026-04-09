<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

final readonly class CategoryData
{
    /**
     * @param  array<string, mixed>  $metadata
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $id,
        public ?string $companyId,
        public string $name,
        public ?string $slug,
        public ?string $color,
        public array $metadata,
        public bool $isActive,
        public bool $isShared,
        public ?string $parentId,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public array $raw = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            companyId: $data['company_id'] ?? null,
            name: (string) ($data['name'] ?? ''),
            slug: $data['slug'] ?? null,
            color: $data['color'] ?? null,
            metadata: (array) ($data['metadata'] ?? []),
            isActive: (bool) ($data['is_active'] ?? false),
            isShared: (bool) ($data['is_shared'] ?? false),
            parentId: $data['parent_id'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            raw: $data,
        );
    }
}
