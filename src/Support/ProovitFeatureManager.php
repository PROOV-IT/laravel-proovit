<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Support;

use Proovit\LaravelProovit\Config\ProovitConfig;

final class ProovitFeatureManager
{
    public function __construct(
        private readonly ProovitConfig $config,
    ) {}

    public function enabled(string $feature, bool $default = false): bool
    {
        return $this->config->featureEnabled($feature, $default);
    }

    public function proofs(): bool
    {
        return $this->enabled('proofs');
    }

    public function certificates(): bool
    {
        return $this->enabled('certificates');
    }

    public function exports(): bool
    {
        return $this->enabled('exports');
    }

    public function audit(): bool
    {
        return $this->enabled('audit');
    }

    public function all(): array
    {
        return $this->config->features;
    }
}
