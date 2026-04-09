<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

final readonly class TokenBalanceData
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public int $balance,
        public ?string $companyId = null,
        public ?int $userId = null,
        public array $raw = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            balance: (int) ($data['balance'] ?? $data['tokens'] ?? 0),
            companyId: $data['company_id'] ?? null,
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            raw: $data,
        );
    }
}
