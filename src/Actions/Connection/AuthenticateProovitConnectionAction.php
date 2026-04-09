<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Connection;

use Closure;
use InvalidArgumentException;
use GuzzleHttp\ClientInterface;
use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\DTOs\ProovitConnectionData;
use Proovit\LaravelProovit\Http\ProovitApiClient;
use Proovit\LaravelProovit\Support\ProovitClientFactory;

final class AuthenticateProovitConnectionAction
{
    public function __construct(
        private readonly ProovitConfig $config,
        private readonly ?Closure $clientFactory = null,
    ) {}

    public function handle(string $email, string $password): ProovitConnectionData
    {
        $loginClient = new ProovitApiClient($this->makeClient($this->config));
        $loginPayload = $loginClient->request('POST', '/v1/auth/login', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        $token = (string) ($loginPayload['token'] ?? $loginPayload['data']['token'] ?? '');
        if ($token === '') {
            throw new InvalidArgumentException('The ProovIT login response did not include a bearer token.');
        }

        $authenticatedConfig = new ProovitConfig(
            baseUrl: $this->config->baseUrl,
            appUrl: $this->config->appUrl,
            apiKey: $this->config->apiKey,
            accessToken: $token,
            selectedCompanyUuid: $this->config->selectedCompanyUuid,
            workspaceToken: $this->config->workspaceToken,
            companyName: $this->config->companyName,
            loginEmail: $email,
            companies: $this->config->companies,
            mode: $this->config->mode,
            timeout: $this->config->timeout,
            connectTimeout: $this->config->connectTimeout,
            verifyTls: $this->config->verifyTls,
            retryAttempts: $this->config->retryAttempts,
            retrySleepMs: $this->config->retrySleepMs,
            healthEndpoint: $this->config->healthEndpoint,
            api: $this->config->api,
            features: $this->config->features,
            certificates: $this->config->certificates,
            exports: $this->config->exports,
            audit: $this->config->audit,
            docs: $this->config->docs,
        );

        $companiesClient = new ProovitApiClient($this->makeClient($authenticatedConfig));
        $companiesPayload = $companiesClient->request('GET', '/v1/companies');
        $companies = array_values((array) ($companiesPayload['data'] ?? $companiesPayload['items'] ?? $companiesPayload['companies'] ?? $companiesPayload));

        return ProovitConnectionData::fromArray([
            'connected' => true,
            'mode' => $this->config->mode->value,
            'base_url' => $this->config->baseUrl,
            'bearer_token' => $token,
            'selected_company_uuid' => null,
            'workspace_token' => null,
            'company_name' => null,
            'login_email' => $email,
            'companies' => $companies,
            'payload' => $loginPayload,
        ]);
    }

    private function makeClient(ProovitConfig $config): ClientInterface
    {
        if ($this->clientFactory instanceof Closure) {
            $client = ($this->clientFactory)($config);
            if ($client instanceof ClientInterface) {
                return $client;
            }
        }

        return (new ProovitClientFactory)->make($config);
    }
}
