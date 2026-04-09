<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Exceptions;

class ApiException extends ProovitException
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly array $payload = []
    ) {
        parent::__construct($message, $statusCode);
    }
}
