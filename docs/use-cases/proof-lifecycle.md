# Proof lifecycle

```php
$client = app(\Proovit\LaravelProovit\ProovitClient::class)->withAccessToken($token);

$proof = $client->proofs()->init([
    'name' => 'Contract proof',
    'description' => 'Deposit a signed contract',
]);

$client->proofs()->uploadFiles($proof->id, [
    [
        'name' => 'files[]',
        'contents' => fopen(storage_path('app/contract.pdf'), 'r'),
        'filename' => 'contract.pdf',
        'mime_type' => 'application/pdf',
    ],
]);
```
