<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Events\Proofs;

final readonly class ProofDeleted
{
    /**
     * @param  array<string, mixed>  $response
     */
    public function __construct(
        public string $proofId,
        public bool $deleted = true,
        public array $response = [],
    ) {}
}
