<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

final readonly class ProofCertificateData
{
    public function __construct(
        public string $proofId,
        public ?string $url = null,
        public ?string $downloadedAt = null,
        public array $raw = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            proofId: (string) ($data['proof_id'] ?? $data['id'] ?? ''),
            url: $data['url'] ?? $data['certificate_url'] ?? null,
            downloadedAt: $data['downloaded_at'] ?? null,
            raw: $data,
        );
    }
}
