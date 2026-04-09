<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Resources;

use Proovit\LaravelProovit\Actions\Proofs\ListProofTemplatesAction;

final class ProofTemplateResource
{
    public function __construct(
        private readonly ListProofTemplatesAction $listAction,
    ) {}

    public function list(array $query = []): array
    {
        return $this->listAction->handle($query);
    }
}
