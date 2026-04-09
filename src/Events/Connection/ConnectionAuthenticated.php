<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Events\Connection;

use Proovit\LaravelProovit\DTOs\ProovitConnectionData;

final readonly class ConnectionAuthenticated
{
    public function __construct(
        public ProovitConnectionData $connection,
    ) {}
}
