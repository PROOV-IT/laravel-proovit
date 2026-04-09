<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Events\Tokens;

use Proovit\LaravelProovit\DTOs\TokenReservationData;

final readonly class TokenReserved
{
    /**
     * @param  array<string, mixed>  $response
     */
    public function __construct(
        public TokenReservationData $reservation,
        public array $response = [],
    ) {}
}
