<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Support;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Proovit\LaravelProovit\Config\ProovitConfig;

final class ProovitClientFactory
{
    public function make(ProovitConfig $config): ClientInterface
    {
        return new Client([
            'base_uri' => $config->baseUrl,
            'timeout' => $config->timeout,
            'connect_timeout' => $config->connectTimeout,
            'verify' => $config->verifyTls,
            'headers' => $config->headers(),
        ]);
    }
}
