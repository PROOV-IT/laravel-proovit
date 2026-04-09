<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

final readonly class ProovitConnectionData
{
    public function __construct(
        public bool $connected,
        public ?string $mode = null,
        public ?string $baseUrl = null,
        public ?string $workspaceToken = null,
        public array $raw = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            connected: (bool) ($data['connected'] ?? true),
            mode: $data['mode'] ?? null,
            baseUrl: $data['base_url'] ?? null,
            workspaceToken: $data['workspace_token'] ?? null,
            raw: $data,
        );
    }
}
