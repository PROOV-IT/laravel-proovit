<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

final readonly class ProofFileData
{
    public function __construct(
        public string $name,
        public string $contents,
        public ?string $filename = null,
        public ?string $mimeType = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? 'files[]'),
            contents: (string) ($data['contents'] ?? ''),
            filename: $data['filename'] ?? null,
            mimeType: $data['mime_type'] ?? null,
        );
    }
}
