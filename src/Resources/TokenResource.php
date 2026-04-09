<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Resources;

use Proovit\LaravelProovit\Actions\Tokens\GetTokenBalanceAction;
use Proovit\LaravelProovit\Actions\Tokens\ReserveTokenAction;
use Proovit\LaravelProovit\DTOs\TokenBalanceData;
use Proovit\LaravelProovit\DTOs\TokenReservationData;

final class TokenResource
{
    public function __construct(
        private readonly GetTokenBalanceAction $balanceAction,
        private readonly ReserveTokenAction $reserveAction,
    ) {}

    public function balance(): TokenBalanceData
    {
        return $this->balanceAction->handle();
    }

    public function reserve(): TokenReservationData
    {
        return $this->reserveAction->handle();
    }
}
