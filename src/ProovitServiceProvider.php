<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit;
use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\Support\ProovitClientFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class ProovitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-proovit')
            ->hasConfigFile('proovit')
            ->hasTranslations();
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(ProovitConfig::class, static function (): ProovitConfig {
            /** @var array<string, mixed> $config */
            $config = config('proovit', []);

            return ProovitConfig::fromArray($config);
        });

        $this->app->singleton(ProovitClient::class, static function ($app): ProovitClient {
            return new ProovitClient(
                $app->make(ProovitConfig::class),
                $app->make(ProovitClientFactory::class),
            );
        });
    }
}
