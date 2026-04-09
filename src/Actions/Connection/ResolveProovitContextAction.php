<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Actions\Connection;

use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\DTOs\ProovitContextData;
use Proovit\LaravelProovit\Support\ProovitFeatureManager;

final class ResolveProovitContextAction
{
    public function __construct(
        private readonly ProovitConfig $config,
        private readonly ProovitFeatureManager $features,
    ) {}

    public function handle(): ProovitContextData
    {
        $config = $this->config->toArray();

        return ProovitContextData::fromArray([
            'base_url' => $this->config->baseUrl,
            'app_url' => $this->config->appUrl,
            'company_name' => $this->config->companyName,
            'login_email' => $this->config->loginEmail,
            'selected_company_uuid' => $this->config->selectedCompanyUuid,
            'companies' => $this->config->companies,
            'mode' => $this->config->mode->value,
            'features' => $this->features->all(),
            'docs' => $this->config->docs,
            'connection' => $config['connection'] ?? [],
        ]);
    }
}
