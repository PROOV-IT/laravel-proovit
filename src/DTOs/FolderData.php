<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

final readonly class FolderData
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $id,
        public ?string $companyId,
        public string $name,
        public ?string $slug,
        public ?string $parentId,
        public bool $isActive,
        public ?string $visibilityLevel,
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
            parentId: $data['parent_id'] ?? null,
            isActive: (bool) ($data['is_active'] ?? false),
            visibilityLevel: $data['visibility_level'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            raw: $data,
        );
    }
}
