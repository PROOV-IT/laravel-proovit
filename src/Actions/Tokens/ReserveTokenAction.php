<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Tokens;

use Proovit\LaravelProovit\DTOs\TokenReservationData;
use Proovit\LaravelProovit\Events\Tokens\TokenReserved;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class ReserveTokenAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(): TokenReservationData
    {
        $response = $this->client->request('POST', '/v1/tokens/reserve');

        $reservation = TokenReservationData::fromArray($response['data'] ?? $response);
        event(new TokenReserved($reservation, $response));

        return $reservation;
    }
}
