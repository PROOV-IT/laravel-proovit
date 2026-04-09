<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Events\Proofs;

use Proovit\LaravelProovit\DTOs\ProofData;

final readonly class ProofInitialized
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $response
     */
    public function __construct(
        public ProofData $proof,
        public array $payload = [],
        public array $response = [],
    ) {}
}
