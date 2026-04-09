<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit;

use GuzzleHttp\ClientInterface;
use Proovit\LaravelProovit\Actions\Connection\AuthenticateProovitConnectionAction;
use Proovit\LaravelProovit\Actions\Connection\ResolveProovitContextAction;
use Proovit\LaravelProovit\Actions\Connection\TestProovitConnectionAction;
use Proovit\LaravelProovit\Actions\Proofs\DeleteProofAction;
use Proovit\LaravelProovit\Actions\Proofs\DownloadProofCertificateAction;
use Proovit\LaravelProovit\Actions\Proofs\GetProofCertificateLinkAction;
use Proovit\LaravelProovit\Actions\Proofs\GetProofHistoryAction;
use Proovit\LaravelProovit\Actions\Proofs\InitializeProofAction;
use Proovit\LaravelProovit\Actions\Proofs\ListProofsAction;
use Proovit\LaravelProovit\Actions\Proofs\ListProofTemplatesAction;
use Proovit\LaravelProovit\Actions\Proofs\RevokeProofAction;
use Proovit\LaravelProovit\Actions\Proofs\ShowProofAction;
use Proovit\LaravelProovit\Actions\Proofs\SignProofAction;
use Proovit\LaravelProovit\Actions\Proofs\UploadProofFilesAction;
use Proovit\LaravelProovit\Builders\Proofs\ProofBuilder;
use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\Http\ProovitApiClient;
use Proovit\LaravelProovit\Resources\ConnectionResource;
use Proovit\LaravelProovit\Resources\ProofResource;
use Proovit\LaravelProovit\Resources\ProofTemplateResource;
use Proovit\LaravelProovit\Support\ProovitCertificateResolver;
use Proovit\LaravelProovit\Support\ProovitClientFactory;
use Proovit\LaravelProovit\Support\ProovitFeatureManager;

final class ProovitClient
{
    private ?ClientInterface $http = null;

    private ?string $accessToken = null;

    public function __construct(
        private ProovitConfig $config,
        private readonly ProovitClientFactory $factory = new ProovitClientFactory,
        private readonly ProovitCertificateResolver $certificateResolver = new ProovitCertificateResolver,
    ) {
        $this->config->validate();
    }

    public function config(): ProovitConfig
    {
        return $this->config;
    }

    public function withAccessToken(?string $token): self
    {
        $this->accessToken = $token;
        $this->http = null;

        return $this;
    }

    public function connection(): ConnectionResource
    {
        return new ConnectionResource(
            new TestProovitConnectionAction($this->apiClient(), $this->config),
            new ResolveProovitContextAction(
                $this->config,
                $this->features(),
            ),
            new AuthenticateProovitConnectionAction(
                $this->config,
                fn (ProovitConfig $config): ClientInterface => $this->factory->make($config),
            ),
        );
    }

    public function proofs(): ProofResource
    {
        $api = $this->apiClient();

        return new ProofResource(
            new ListProofsAction($api),
            new InitializeProofAction($api),
            new UploadProofFilesAction($api),
            new SignProofAction($api),
            new ShowProofAction($api),
            new GetProofHistoryAction($api),
            new GetProofCertificateLinkAction($api),
            new DownloadProofCertificateAction($api),
            new RevokeProofAction($api),
            new DeleteProofAction($api),
            $this->certificateResolver,
        );
    }

    public function proofTemplates(): ProofTemplateResource
    {
        return new ProofTemplateResource(
            new ListProofTemplatesAction($this->apiClient()),
        );
    }

    public function proofBuilder(): ProofBuilder
    {
        return $this->proofs()->builder();
    }

    public function features(): ProovitFeatureManager
    {
        return new ProovitFeatureManager($this->config);
    }

    private function apiClient(): ProovitApiClient
    {
        return new ProovitApiClient($this->httpClient());
    }

    private function httpClient(): ClientInterface
    {
        if ($this->http === null) {
            $config = $this->config;

            if ($this->accessToken !== null) {
                $config = new ProovitConfig(
                    baseUrl: $config->baseUrl,
                    appUrl: $config->appUrl,
                    apiKey: $config->apiKey,
                    accessToken: $this->accessToken,
                    selectedCompanyUuid: $config->selectedCompanyUuid,
                    workspaceToken: $config->workspaceToken,
                    companyName: $config->companyName,
                    loginEmail: $config->loginEmail,
                    companies: $config->companies,
                    mode: $config->mode,
                    timeout: $config->timeout,
                    connectTimeout: $config->connectTimeout,
                    verifyTls: $config->verifyTls,
                    retryAttempts: $config->retryAttempts,
                    retrySleepMs: $config->retrySleepMs,
                    healthEndpoint: $config->healthEndpoint,
                );
            }

            $this->http = $this->factory->make($config);
        }

        return $this->http;
    }
}
