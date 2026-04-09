# Proof templates

ProovIT exposes a proof template catalog that you can use to build the proof creation form dynamically.

## Endpoint

The SDK reads templates from:

```http
GET /api/v1/proof-templates?per_page=100
```

## SDK usage

```php
use Proovit\LaravelProovit\ProovitClient;

$client = app(ProovitClient::class);

$templates = $client->proofTemplates()->list([
    'per_page' => 100,
]);
```

## Why it matters

- templates define whether a proof requires a signature
- templates describe custom fields and their types
- templates expose required file families
- templates can be used to shape the UI before the proof is initialized

## Common pattern

1. load the templates for the connected company
2. let the user pick a template
3. render the proof form according to the template schema
4. submit the initialization payload through the fluent proof builder

## Notes

- template catalogs are typically cached by the consumer application
- the SDK returns the raw API payload through the resource layer so you can adapt the UI to your own needs
