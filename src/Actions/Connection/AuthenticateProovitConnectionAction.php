<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Connection;

use Closure;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
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

    public function handle(string $email, string $password, ?ProovitConfig $config = null): ProovitConnectionData
    {
        $config ??= $this->config;

        $loginClient = new ProovitApiClient($this->makeClient($config));
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
            baseUrl: $config->baseUrl,
            appUrl: $config->appUrl,
            apiKey: $config->apiKey,
            accessToken: $token,
            selectedCompanyUuid: $config->selectedCompanyUuid,
            workspaceToken: $config->workspaceToken,
            companyName: $config->companyName,
            loginEmail: $email,
            companies: $config->companies,
            mode: $config->mode,
            timeout: $config->timeout,
            connectTimeout: $config->connectTimeout,
            verifyTls: $config->verifyTls,
            retryAttempts: $config->retryAttempts,
            retrySleepMs: $config->retrySleepMs,
            healthEndpoint: $config->healthEndpoint,
            api: $config->api,
            features: $config->features,
            certificates: $config->certificates,
            exports: $config->exports,
            audit: $config->audit,
            docs: $config->docs,
        );

        $companiesClient = new ProovitApiClient($this->makeClient($authenticatedConfig));
        $companiesPayload = $companiesClient->request('GET', '/v1/companies');
        $companies = array_values((array) ($companiesPayload['data'] ?? $companiesPayload['items'] ?? $companiesPayload['companies'] ?? $companiesPayload));

        return ProovitConnectionData::fromArray([
            'connected' => true,
            'mode' => $config->mode->value,
            'base_url' => $config->baseUrl,
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
