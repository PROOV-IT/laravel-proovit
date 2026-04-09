<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

final readonly class ProofData
{
    public function __construct(
        public string $id,
        public string $status,
        public ?string $name = null,
        public ?string $description = null,
        public array $files = [],
        public array $metadata = [],
        public ?string $signedAt = null,
        public ?string $certificateUrl = null,
        public ?array $folder = null,
        public ?array $category = null,
        public ?array $template = null,
        public ?array $company = null,
        public ?array $user = null,
        public ?array $links = null,
        public ?int $filesCount = null,
        public array $raw = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            status: (string) ($data['status'] ?? 'unknown'),
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            files: array_values((array) ($data['files'] ?? [])),
            metadata: (array) ($data['metadata'] ?? []),
            signedAt: $data['signed_at'] ?? null,
            certificateUrl: $data['certificate_url'] ?? ($data['links']['certificate_pdf'] ?? null),
            folder: isset($data['folder']) && is_array($data['folder']) ? $data['folder'] : null,
            category: isset($data['category']) && is_array($data['category']) ? $data['category'] : null,
            template: isset($data['template']) && is_array($data['template']) ? $data['template'] : null,
            company: isset($data['company']) && is_array($data['company']) ? $data['company'] : null,
            user: isset($data['user']) && is_array($data['user']) ? $data['user'] : null,
            links: isset($data['links']) && is_array($data['links']) ? $data['links'] : null,
            filesCount: isset($data['files_count']) ? (int) $data['files_count'] : null,
            raw: $data,
        );
    }
}
