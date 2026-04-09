<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Resources;

use Proovit\LaravelProovit\Actions\Connection\AuthenticateProovitConnectionAction;
use Proovit\LaravelProovit\Actions\Connection\ResolveProovitContextAction;
use Proovit\LaravelProovit\Actions\Connection\TestProovitConnectionAction;
use Proovit\LaravelProovit\DTOs\ProovitConnectionData;
use Proovit\LaravelProovit\DTOs\ProovitContextData;

final class ConnectionResource
{
    public function __construct(
        private readonly TestProovitConnectionAction $action,
        private readonly ResolveProovitContextAction $contextAction,
        private readonly AuthenticateProovitConnectionAction $authenticateAction,
    ) {}

    public function test(): ProovitConnectionData
    {
        return $this->action->handle();
    }

    public function authenticate(string $email, string $password): ProovitConnectionData
    {
        return $this->authenticateAction->handle($email, $password);
    }

    public function context(): ProovitContextData
    {
        return $this->contextAction->handle();
    }
}
