<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Resources;

use Proovit\LaravelProovit\Actions\Connection\TestProovitConnectionAction;
use Proovit\LaravelProovit\DTOs\ProovitConnectionData;

final class ConnectionResource
{
    public function __construct(
        private readonly TestProovitConnectionAction $action,
    ) {}

    public function test(): ProovitConnectionData
    {
        return $this->action->handle();
    }
}
