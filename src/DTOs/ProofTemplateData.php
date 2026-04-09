<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

use Illuminate\Support\Arr;

final readonly class ProofTemplateData
{
    /**
     * @param  array<string, mixed>  $dataSchema
     * @param  array<string, mixed>  $metadata
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $id,
        public ?string $companyId,
        public string $name,
        public ?string $slug,
        public ?string $description,
        public array $dataSchema,
        public array $metadata,
        public bool $isActive,
        public bool $default,
        public bool $isImportable,
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
            description: $data['description'] ?? null,
            dataSchema: (array) ($data['data_schema'] ?? []),
            metadata: (array) ($data['metadata'] ?? []),
            isActive: (bool) ($data['is_active'] ?? false),
            default: (bool) ($data['default'] ?? false),
            isImportable: (bool) ($data['is_importable'] ?? false),
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            raw: $data,
        );
    }

    public function requiresSignature(): bool
    {
        return (bool) Arr::get($this->dataSchema, 'signature', false);
    }

    public function isShared(): bool
    {
        return (bool) Arr::get($this->dataSchema, 'shared', false);
    }

    public function displayFolders(): bool
    {
        return (bool) Arr::get($this->dataSchema, 'displayFolders', false);
    }

    public function displayCategories(): bool
    {
        return (bool) Arr::get($this->dataSchema, 'displayCategories', false);
    }

    public function displayPrefilledFields(): bool
    {
        return (bool) Arr::get($this->dataSchema, 'displayPrefilledFields', false);
    }

    public function displayTags(): bool
    {
        return (bool) Arr::get($this->dataSchema, 'displayTags', false);
    }

    public function displayUploadForm(): ?string
    {
        $value = Arr::get($this->dataSchema, 'displayUploadForm');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function customFields(): array
    {
        return array_map(
            static fn (array $field): ProofTemplateCustomFieldData => ProofTemplateCustomFieldData::fromArray($field),
            array_values((array) Arr::get($this->dataSchema, 'customFields', [])),
        );
    }

    /**
     * @return array<int, string>
     */
    public function requiredFiles(): array
    {
        return array_values(array_filter(array_map(
            'strval',
            (array) Arr::get($this->dataSchema, 'requiredFiles', []),
        )));
    }
}
