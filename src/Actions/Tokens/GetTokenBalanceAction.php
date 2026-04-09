<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Tokens;

use Proovit\LaravelProovit\DTOs\TokenBalanceData;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class GetTokenBalanceAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
    ) {}

    public function handle(): TokenBalanceData
    {
        $response = $this->client->request('GET', '/v1/tokens/balance');

        return TokenBalanceData::fromArray($response['data'] ?? $response);
    }
}
