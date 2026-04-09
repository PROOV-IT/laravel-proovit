<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Support;

use Illuminate\Support\Str;

final class ProovitWebhookVerifier
{
    public function verify(string $payload, string $signature, string $secret): bool
    {
        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, Str::lower(trim($signature)));
    }
}
