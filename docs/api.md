# API

The package is a Laravel SDK, not a public HTTP API.

Use the `ProovitClient` service from the container and call the `connection()` and `proofs()` resources.

## Example

```php
use Proovit\LaravelProovit\ProovitClient;

$client = app(ProovitClient::class)->withAccessToken($token);

$proof = $client->proofs()->init([
    'name' => 'Invoice INV-2026-0001',
    'description' => 'Proof deposit for invoice',
]);
```
