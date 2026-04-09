<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit;

use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\Support\ProovitCertificateResolver;
use Proovit\LaravelProovit\Support\ProovitClientFactory;
use Proovit\LaravelProovit\Support\ProovitConfigResolver;
use Proovit\LaravelProovit\Support\ProovitFeatureManager;
use Proovit\LaravelProovit\Support\ProovitPayloadNormalizer;
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

        $this->app->singleton(ProovitConfigResolver::class);
        $this->app->singleton(ProovitConfig::class, static fn ($app): ProovitConfig => $app->make(ProovitConfigResolver::class)->resolve());
        $this->app->singleton(ProovitFeatureManager::class, static fn ($app): ProovitFeatureManager => new ProovitFeatureManager($app->make(ProovitConfig::class)));
        $this->app->singleton(ProovitCertificateResolver::class);

        $this->app->singleton(ProovitClient::class, static function ($app): ProovitClient {
            return new ProovitClient(
                $app->make(ProovitConfig::class),
                $app->make(ProovitClientFactory::class),
                $app->make(ProovitPayloadNormalizer::class),
                $app->make(ProovitCertificateResolver::class),
            );
        });
    }
}
