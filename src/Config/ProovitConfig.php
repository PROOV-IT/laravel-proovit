<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Config;

use InvalidArgumentException;
use Proovit\LaravelProovit\Enums\ProovitMode;

final readonly class ProovitConfig
{
    public function __construct(
        public string $baseUrl,
        public ?string $appUrl = null,
        public ?string $apiKey = null,
        public ?string $accessToken = null,
        public ?string $workspaceToken = null,
        public ProovitMode $mode = ProovitMode::Production,
        public int $timeout = 30,
        public int $connectTimeout = 10,
        public bool $verifyTls = true,
        public int $retryAttempts = 0,
        public int $retrySleepMs = 250,
        public string $healthEndpoint = '/v1/health',
    ) {
        $this->baseUrl = rtrim($this->baseUrl, '/');
        $this->appUrl = $this->appUrl !== null ? rtrim($this->appUrl, '/') : null;
        $this->healthEndpoint = '/' . ltrim($this->healthEndpoint, '/');
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
            workspaceToken: $connection['workspace_token'] ?? null,
            mode: $proovitMode,
            timeout: (int) ($connection['timeout'] ?? 30),
            connectTimeout: (int) ($connection['connect_timeout'] ?? 10),
            verifyTls: (bool) ($connection['verify_tls'] ?? true),
            retryAttempts: (int) ($connection['retry_attempts'] ?? 0),
            retrySleepMs: (int) ($connection['retry_sleep_ms'] ?? 250),
            healthEndpoint: (string) ($connection['health_endpoint'] ?? '/v1/health'),
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
                'workspace_token' => $this->workspaceToken,
                'mode' => $this->mode->value,
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connectTimeout,
                'verify_tls' => $this->verifyTls,
                'retry_attempts' => $this->retryAttempts,
                'retry_sleep_ms' => $this->retrySleepMs,
                'health_endpoint' => $this->healthEndpoint,
            ],
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

        if ($this->workspaceToken !== null && $this->workspaceToken !== '') {
            $headers['X-WORKSPACE-TOKEN'] = $this->workspaceToken;
        }

        return $headers;
    }
}
