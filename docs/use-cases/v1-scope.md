# ProovIT v1 scope

Version 1 of `proovit/laravel-proovit` focuses on the proof lifecycle and the minimum
integration surface needed by production applications.

## Included in v1

- connection test against the ProovIT API
- local context resolution from configuration
- proof initialization
- proof file upload
- proof signing
- proof listing and lookup
- proof history retrieval
- certificate link retrieval
- certificate download
- proof revocation
- proof deletion

## Excluded from v1

- DocumentHub-specific workflows
- advanced export orchestration
- custom collaboration flows
- any UI coupling to Filament

## SDK example

```php
$client = app(\Proovit\LaravelProovit\ProovitClient::class)->withAccessToken($token);

$context = $client->connection()->context();
$proof = $client->proofs()->init([
    'name' => 'Invoice INV-2026-0001',
    'description' => 'Deposit a proof for the invoice archive',
]);

$client->proofs()->uploadFiles($proof->id, [
    [
        'name' => 'files[]',
        'contents' => fopen(storage_path('app/invoices/invoice.pdf'), 'r'),
        'filename' => 'invoice.pdf',
        'mime_type' => 'application/pdf',
    ],
]);
```
