<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Config;

use InvalidArgumentException;
use Proovit\LaravelProovit\Enums\ProovitMode;

final class ProovitConfig
{
    public readonly string $baseUrl;

    public readonly ?string $appUrl;

    public readonly ?string $apiKey;

    public readonly ?string $accessToken;

    public readonly ?string $selectedCompanyUuid;

    public readonly ?string $workspaceToken;

    public readonly ?string $companyName;

    public readonly ?string $loginEmail;

    public readonly array $companies;

    public readonly ProovitMode $mode;

    public readonly int $timeout;

    public readonly int $connectTimeout;

    public readonly bool $verifyTls;

    public readonly int $retryAttempts;

    public readonly int $retrySleepMs;

    public readonly string $healthEndpoint;

    public readonly array $api;

    public readonly array $features;

    public readonly array $certificates;

    public readonly array $exports;

    public readonly array $audit;

    public readonly array $docs;

    public function __construct(
        string $baseUrl,
        ?string $appUrl = null,
        ?string $apiKey = null,
        ?string $accessToken = null,
        ?string $selectedCompanyUuid = null,
        ?string $workspaceToken = null,
        ?string $companyName = null,
        ?string $loginEmail = null,
        array $companies = [],
        ProovitMode $mode = ProovitMode::Production,
        int $timeout = 30,
        int $connectTimeout = 10,
        bool $verifyTls = true,
        int $retryAttempts = 0,
        int $retrySleepMs = 250,
        string $healthEndpoint = '/v1/health',
        array $api = [],
        array $features = [],
        array $certificates = [],
        array $exports = [],
        array $audit = [],
        array $docs = [],
        ) {
        $this->baseUrl = self::normalizeBaseUrl($baseUrl);
        $this->appUrl = $appUrl !== null ? rtrim($appUrl, '/') : null;
        $this->apiKey = $apiKey;
        $this->accessToken = $accessToken;
        $this->selectedCompanyUuid = $selectedCompanyUuid !== null && $selectedCompanyUuid !== '' ? $selectedCompanyUuid : ($workspaceToken !== null && $workspaceToken !== '' ? $workspaceToken : null);
        $this->workspaceToken = $workspaceToken !== null && $workspaceToken !== '' ? $workspaceToken : $this->selectedCompanyUuid;
        $this->companyName = $companyName;
        $this->loginEmail = $loginEmail;
        $this->companies = $companies;
        $this->mode = $mode;
        $this->timeout = $timeout;
        $this->connectTimeout = $connectTimeout;
        $this->verifyTls = $verifyTls;
        $this->retryAttempts = $retryAttempts;
        $this->retrySleepMs = $retrySleepMs;
        $this->healthEndpoint = '/'.ltrim($healthEndpoint, '/');
        $this->api = $api;
        $this->features = $features;
        $this->certificates = $certificates;
        $this->exports = $exports;
        $this->audit = $audit;
        $this->docs = $docs;
    }

    public static function fromArray(array $config): self
    {
        $connection = $config['connection'] ?? $config;

        $baseUrl = (string) ($connection['base_url'] ?? '');
        if ($baseUrl === '') {
            throw new InvalidArgumentException('The ProovIT base URL is required.');
        }

        $mode = (string) ($connection['mode'] ?? ProovitMode::Production->value);

        try {
            $proovitMode = ProovitMode::from($mode);
        } catch (\ValueError $exception) {
            throw new InvalidArgumentException(sprintf(
                "Invalid ProovIT mode '%s'. Allowed values are: %s.",
                $mode,
                implode(', ', array_map(static fn (ProovitMode $case): string => $case->value, ProovitMode::cases()))
            ), previous: $exception);
        }

        return new self(
            baseUrl: $baseUrl,
            appUrl: $connection['app_url'] ?? null,
            apiKey: $connection['api_key'] ?? null,
            accessToken: $connection['access_token'] ?? null,
            selectedCompanyUuid: $connection['selected_company_uuid'] ?? $connection['workspace_token'] ?? null,
            workspaceToken: $connection['workspace_token'] ?? null,
            companyName: $connection['company_name'] ?? null,
            loginEmail: $connection['login_email'] ?? null,
            companies: (array) ($connection['companies'] ?? []),
            mode: $proovitMode,
            timeout: (int) ($connection['timeout'] ?? 30),
            connectTimeout: (int) ($connection['connect_timeout'] ?? 10),
            verifyTls: (bool) ($connection['verify_tls'] ?? true),
            retryAttempts: (int) ($connection['retry_attempts'] ?? 0),
            retrySleepMs: (int) ($connection['retry_sleep_ms'] ?? 250),
            healthEndpoint: (string) ($connection['health_endpoint'] ?? '/v1/health'),
            api: (array) ($config['api'] ?? []),
            features: (array) ($config['features'] ?? []),
            certificates: (array) ($config['certificates'] ?? []),
            exports: (array) ($config['exports'] ?? []),
            audit: (array) ($config['audit'] ?? []),
            docs: (array) ($config['docs'] ?? []),
        );
    }

    public function validate(): void
    {
        if (filter_var($this->baseUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('The ProovIT base URL must be a valid URL.');
        }

        if ($this->appUrl !== null && filter_var($this->appUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('The ProovIT app URL must be a valid URL.');
        }
    }

    public function toArray(): array
    {
        return [
            'connection' => [
                'base_url' => $this->baseUrl,
                'app_url' => $this->appUrl,
                'api_key' => $this->apiKey,
                'access_token' => $this->accessToken,
                'selected_company_uuid' => $this->selectedCompanyUuid,
                'workspace_token' => $this->workspaceToken ?? $this->selectedCompanyUuid,
                'company_name' => $this->companyName,
                'login_email' => $this->loginEmail,
                'companies' => $this->companies,
                'mode' => $this->mode->value,
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connectTimeout,
                'verify_tls' => $this->verifyTls,
                'retry_attempts' => $this->retryAttempts,
                'retry_sleep_ms' => $this->retrySleepMs,
                'health_endpoint' => $this->healthEndpoint,
            ],
            'api' => $this->api,
            'features' => $this->features,
            'certificates' => $this->certificates,
            'exports' => $this->exports,
            'audit' => $this->audit,
            'docs' => $this->docs,
        ];
    }

    public function headers(): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        if ($this->apiKey !== null && $this->apiKey !== '') {
            $headers['X-API-KEY'] = $this->apiKey;
        }

        if ($this->accessToken !== null && $this->accessToken !== '') {
            $headers['Authorization'] = sprintf('Bearer %s', $this->accessToken);
        }

        $selectedCompanyUuid = $this->selectedCompanyUuid ?? $this->workspaceToken;

        if ($selectedCompanyUuid !== null && $selectedCompanyUuid !== '') {
            $headers['X-COMPANY-TOKEN'] = $selectedCompanyUuid;
            $headers['X-COMPANY-ID'] = $selectedCompanyUuid;
        }

        return $headers;
    }

    private static function normalizeBaseUrl(string $baseUrl): string
    {
        $baseUrl = rtrim($baseUrl, '/');

        if ($baseUrl === '') {
            return $baseUrl;
        }

        $path = parse_url($baseUrl, PHP_URL_PATH);
        if (is_string($path) && $path !== '' && $path !== '/') {
            return $baseUrl;
        }

        return $baseUrl.'/api';
    }

    public function featureEnabled(string $feature, bool $default = false): bool
    {
        return (bool) ($this->features[$feature] ?? $default);
    }
}
