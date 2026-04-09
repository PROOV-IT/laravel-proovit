<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

use Proovit\LaravelProovit\Enums\ProovitMode;

final readonly class ProovitContextData
{
    public function __construct(
        public string $baseUrl,
        public ?string $appUrl,
        public ?string $companyName,
        public ?string $loginEmail,
        public ProovitMode $mode,
        public array $features = [],
        public array $docs = [],
        public array $connection = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $mode = $data['mode'] ?? ProovitMode::Production->value;

        return new self(
            baseUrl: (string) ($data['base_url'] ?? ''),
            appUrl: $data['app_url'] ?? null,
            companyName: $data['company_name'] ?? null,
            loginEmail: $data['login_email'] ?? null,
            mode: ProovitMode::from((string) $mode),
            features: (array) ($data['features'] ?? []),
            docs: (array) ($data['docs'] ?? []),
            connection: (array) ($data['connection'] ?? []),
        );
    }
}
