<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Resources;

use Proovit\LaravelProovit\Actions\Proofs\DeleteProofAction;
use Proovit\LaravelProovit\Actions\Proofs\DownloadProofCertificateAction;
use Proovit\LaravelProovit\Actions\Proofs\GetProofCertificateLinkAction;
use Proovit\LaravelProovit\Actions\Proofs\GetProofHistoryAction;
use Proovit\LaravelProovit\Actions\Proofs\InitializeProofAction;
use Proovit\LaravelProovit\Actions\Proofs\ListProofsAction;
use Proovit\LaravelProovit\Actions\Proofs\RevokeProofAction;
use Proovit\LaravelProovit\Actions\Proofs\ShowProofAction;
use Proovit\LaravelProovit\Actions\Proofs\SignProofAction;
use Proovit\LaravelProovit\Actions\Proofs\UploadProofFilesAction;
use Proovit\LaravelProovit\Builders\Proofs\ProofBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofFilesBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofSignatureBuilder;
use Proovit\LaravelProovit\DTOs\ProofCertificateData;
use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\Support\ProovitCertificateResolver;

final class ProofResource
{
    public function __construct(
        private readonly ListProofsAction $listAction,
        private readonly InitializeProofAction $initializeAction,
        private readonly UploadProofFilesAction $uploadAction,
        private readonly SignProofAction $signAction,
        private readonly ShowProofAction $showAction,
        private readonly GetProofHistoryAction $historyAction,
        private readonly GetProofCertificateLinkAction $certificateLinkAction,
        private readonly DownloadProofCertificateAction $downloadCertificateAction,
        private readonly RevokeProofAction $revokeAction,
        private readonly DeleteProofAction $deleteAction,
        private readonly ProovitCertificateResolver $certificateResolver,
    ) {}

    public function list(array $query = []): array
    {
        return $this->listAction->handle($query);
    }

    public function builder(): ProofBuilder
    {
        return new ProofBuilder;
    }

    public function init(array|ProofBuilder $payload): ProofData
    {
        return $this->initializeAction->handle($payload);
    }

    public function uploadFiles(string $proofId, array|ProofBuilder|ProofFilesBuilder $files): array
    {
        return $this->uploadAction->handle($proofId, $files);
    }

    public function sign(string $proofId, string|ProofBuilder|ProofSignatureBuilder|null $signatureBase64 = null, array $clientContext = []): array
    {
        return $this->signAction->handle($proofId, $signatureBase64, $clientContext);
    }

    public function show(string $proofId): ProofData
    {
        return $this->showAction->handle($proofId);
    }

    public function history(string $proofId): array
    {
        return $this->historyAction->handle($proofId);
    }

    public function getCertificateLink(string $proofId): ProofCertificateData
    {
        return $this->certificateLinkAction->handle($proofId);
    }

    public function downloadCertificate(string $proofId): string
    {
        return $this->downloadCertificateAction->handle($proofId);
    }

    public function revoke(string $proofId, ?string $reason = null): array
    {
        return $this->revokeAction->handle($proofId, $reason);
    }

    public function certificateFilename(string $proofId): string
    {
        return $this->certificateResolver->filename(
            $this->certificateLinkAction->handle($proofId)
        );
    }

    public function delete(string $proofId): bool
    {
        return $this->deleteAction->handle($proofId);
    }
}
