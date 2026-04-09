<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Support;

use Proovit\LaravelProovit\Config\ProovitConfig;

final class ProovitConfigResolver
{
    public function __construct(
        private readonly ProovitSettingsRepository $settingsRepository = new ProovitSettingsRepository,
    ) {}

    public function resolve(?array $config = null): ProovitConfig
    {
        return ProovitConfig::fromArray(array_replace_recursive(
            $config ?? config('proovit', []),
            $this->settingsRepository->all(),
        ));
    }
}
