# API

The package is a Laravel SDK, not a public HTTP API.

Use the `ProovitClient` service from the container and call the `connection()` and `proofs()` resources.
For complex proof creation flows, use `proofBuilder()` to assemble the payload for `init`,
`files`, and `sign`.

## Example

```php
use Proovit\LaravelProovit\ProovitClient;

$client = app(ProovitClient::class)->withAccessToken($token);

$proof = $client->proofs()->init([
    'name' => 'Invoice INV-2026-0001',
    'description' => 'Proof deposit for invoice',
]);
```

```php
use Proovit\LaravelProovit\Builders\Proofs\ProofMetadataBuilder;

$proofBuilder = $client->proofBuilder()
    ->withName('Invoice INV-2026-0001')
    ->withMetadata(static function (ProofMetadataBuilder $metadata): void {
        $metadata->withCustomFields(['invoice_number' => 'INV-2026-0001']);
    });
```
