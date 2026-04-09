<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

final readonly class TokenReservationData
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public ?string $reservationId,
        public ?string $status = null,
        public array $raw = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            reservationId: $data['reservation_id'] ?? $data['id'] ?? null,
            status: $data['status'] ?? null,
            raw: $data,
        );
    }
}
