# proovit/laravel-proovit

Laravel 13 SDK for the ProovIT proof deposit and certification service.

## What it does

- centralizes the HTTP client and configuration for the ProovIT service
- lists proof templates for the connected company
- creates and uploads proofs
- signs proofs
- retrieves proof history and certificate links
- exposes the ProovIT workflow through testable actions

## Requirements

- PHP 8.3+
- Laravel 13+
- Guzzle

## Install

```bash
composer require proovit/laravel-proovit
```

## Documentation

- [Install](docs/install.md)
- [Configuration](docs/configuration.md)
- [API](docs/api.md)
- [Use cases](docs/use-cases/)
- [Proof templates](docs/use-cases/proof-templates.md)
- [Reference data](docs/use-cases/reference-data.md)
- [Proof builder](docs/use-cases/proof-builder.md)
- [Persistent configuration](docs/use-cases/settings-page.md)
- [Release notes](docs/release-notes.md)
- [V1 scope](docs/use-cases/v1-scope.md)

## Acknowledgements

- [Laravel](https://laravel.com)
- [Guzzle](https://docs.guzzlephp.org/)
- [Spatie Laravel Package Tools](https://github.com/spatie/laravel-package-tools)
