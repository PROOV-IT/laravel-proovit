<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Connection;

use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\DTOs\ProovitConnectionData;
use Proovit\LaravelProovit\Http\ProovitApiClient;

final class TestProovitConnectionAction
{
    public function __construct(
        private readonly ProovitApiClient $client,
        private readonly ProovitConfig $config,
    ) {}

    public function handle(): ProovitConnectionData
    {
        $payload = $this->client->request('GET', $this->config->healthEndpoint);

        return ProovitConnectionData::fromArray([
            'connected' => true,
            'mode' => $this->config->mode->value,
            'base_url' => $this->config->baseUrl,
            'workspace_token' => $this->config->workspaceToken,
            'company_name' => $this->config->companyName,
            'login_email' => $this->config->loginEmail,
            'payload' => $payload,
        ] + $payload);
    }
}
