<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Resources;

use Proovit\LaravelProovit\Actions\Connection\AuthenticateProovitConnectionAction;
use Proovit\LaravelProovit\Actions\Connection\ResolveProovitContextAction;
use Proovit\LaravelProovit\Actions\Connection\TestProovitConnectionAction;
use Proovit\LaravelProovit\DTOs\ProovitConnectionData;
use Proovit\LaravelProovit\DTOs\ProovitContextData;
use Proovit\LaravelProovit\Support\ProovitSettingsRepository;

final class ConnectionResource
{
    public function __construct(
        private readonly TestProovitConnectionAction $action,
        private readonly ResolveProovitContextAction $contextAction,
        private readonly AuthenticateProovitConnectionAction $authenticateAction,
        private readonly ProovitSettingsRepository $settingsRepository,
    ) {}

    public function test(): ProovitConnectionData
    {
        return $this->action->handle();
    }

    public function authenticate(string $email, string $password): ProovitConnectionData
    {
        return $this->authenticateAction->handle($email, $password);
    }

    public function authenticateAndPersist(string $email, string $password, ?string $selectedCompanyUuid = null): ProovitConnectionData
    {
        return $this->persist(
            $this->authenticate($email, $password),
            $selectedCompanyUuid,
        );
    }

    public function refreshBearer(?string $selectedCompanyUuid = null): ProovitConnectionData
    {
        $settings = $this->settingsRepository->all();
        $connection = ProovitConnectionData::fromArray((array) ($settings['connection'] ?? $settings));

        $email = trim((string) ($connection->loginEmail ?? ''));
        $password = trim((string) (data_get($settings, 'connection.login_password', data_get($settings, 'login_password', ''))));

        if ($email === '' || $password === '') {
            throw new \RuntimeException('Stored ProovIT credentials are required to refresh the bearer token.');
        }

        return $this->persist(
            $this->authenticate($email, $password),
            $selectedCompanyUuid ?? $connection->selectedCompanyUuid ?? $connection->workspaceToken,
        );
    }

    public function persist(ProovitConnectionData|array $connection, ?string $selectedCompanyUuid = null): ProovitConnectionData
    {
        $connectionData = $connection instanceof ProovitConnectionData
            ? $connection
            : ProovitConnectionData::fromArray($connection);

        $resolvedCompanyUuid = $this->resolveCompanyUuid($connectionData, $selectedCompanyUuid);
        $resolvedCompanyName = $this->resolveCompanyName($connectionData, $resolvedCompanyUuid);

        $payload = array_replace_recursive(
            $this->settingsRepository->all(),
            [
                'connection' => array_filter([
                    'base_url' => $connectionData->baseUrl,
                    'access_token' => $connectionData->bearerToken,
                    'selected_company_uuid' => $resolvedCompanyUuid,
                    'workspace_token' => $resolvedCompanyUuid,
                    'company_name' => $resolvedCompanyName,
                    'login_email' => $connectionData->loginEmail,
                    'companies' => $connectionData->companies,
                ], static fn (mixed $value): bool => $value !== null && $value !== ''),
            ],
        );

        $this->settingsRepository->save($payload);

        return ProovitConnectionData::fromArray([
            ...$connectionData->raw,
            'base_url' => $connectionData->baseUrl,
            'bearer_token' => $connectionData->bearerToken,
            'selected_company_uuid' => $resolvedCompanyUuid,
            'workspace_token' => $resolvedCompanyUuid,
            'company_name' => $resolvedCompanyName,
            'login_email' => $connectionData->loginEmail,
            'companies' => $connectionData->companies,
        ]);
    }

    public function selectCompany(string $companyUuid): ProovitConnectionData
    {
        $settings = $this->settingsRepository->all();
        $connection = ProovitConnectionData::fromArray((array) ($settings['connection'] ?? $settings));

        if ($connection->baseUrl === null || $connection->bearerToken === null) {
            throw new \RuntimeException('The ProovIT connection must be authenticated before selecting a company.');
        }

        return $this->persist($connection, $companyUuid);
    }

    public function context(): ProovitContextData
    {
        return $this->contextAction->handle();
    }

    private function resolveCompanyUuid(ProovitConnectionData $connection, ?string $selectedCompanyUuid): ?string
    {
        $selectedCompanyUuid = trim((string) ($selectedCompanyUuid ?? ''));
        if ($selectedCompanyUuid !== '') {
            return $selectedCompanyUuid;
        }

        $current = trim((string) ($connection->selectedCompanyUuid ?? $connection->workspaceToken ?? ''));
        if ($current !== '') {
            return $current;
        }

        return null;
    }

    private function resolveCompanyName(ProovitConnectionData $connection, ?string $selectedCompanyUuid): ?string
    {
        if ($selectedCompanyUuid === null || $selectedCompanyUuid === '') {
            return null;
        }

        foreach ($connection->companies as $company) {
            if (! is_array($company)) {
                continue;
            }

            $uuid = (string) ($company['uuid'] ?? $company['id'] ?? '');
            if ($uuid !== $selectedCompanyUuid) {
                continue;
            }

            $name = trim((string) ($company['name'] ?? ''));

            return $name !== '' ? $name : null;
        }

        return $connection->companyName;
    }
}
