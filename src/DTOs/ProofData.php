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
        public array $metadata = [],
        public ?string $signedAt = null,
        public ?string $certificateUrl = null,
        public array $raw = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            status: (string) ($data['status'] ?? 'unknown'),
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            metadata: (array) ($data['metadata'] ?? []),
            signedAt: $data['signed_at'] ?? null,
            certificateUrl: $data['certificate_url'] ?? null,
            raw: $data,
        );
    }
}
