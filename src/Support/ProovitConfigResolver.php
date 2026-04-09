<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Support;

use Proovit\LaravelProovit\Config\ProovitConfig;

final class ProovitConfigResolver
{
    public function resolve(?array $config = null): ProovitConfig
    {
        return ProovitConfig::fromArray($config ?? config('proovit', []));
    }
}
