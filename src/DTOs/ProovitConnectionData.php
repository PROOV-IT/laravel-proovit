<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

final readonly class ProovitConnectionData
{
    public function __construct(
        public bool $connected,
        public ?string $mode = null,
        public ?string $baseUrl = null,
        public ?string $bearerToken = null,
        public ?string $selectedCompanyUuid = null,
        public ?string $workspaceToken = null,
        public ?string $companyName = null,
        public ?string $loginEmail = null,
        public array $companies = [],
        public array $raw = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            connected: (bool) ($data['connected'] ?? true),
            mode: $data['mode'] ?? null,
            baseUrl: $data['base_url'] ?? null,
            bearerToken: $data['bearer_token'] ?? $data['access_token'] ?? null,
            selectedCompanyUuid: $data['selected_company_uuid'] ?? $data['workspace_token'] ?? null,
            workspaceToken: $data['workspace_token'] ?? null,
            companyName: $data['company_name'] ?? null,
            loginEmail: $data['login_email'] ?? null,
            companies: (array) ($data['companies'] ?? []),
            raw: $data,
        );
    }
}
