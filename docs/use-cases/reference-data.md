# Reference data

The SDK exposes a small set of reusable reference data resources for the connected ProovIT company.

## Categories

```php
use Proovit\LaravelProovit\ProovitClient;

$client = app(ProovitClient::class);

$categories = $client->categories()->options([
    'per_page' => 200,
    'include_shared' => true,
]);
```

## Folders

```php
use Proovit\LaravelProovit\ProovitClient;

$client = app(ProovitClient::class);

$folders = $client->folders()->options([
    'per_page' => 200,
    'accessible_by' => 'write',
]);
```

## Tokens

```php
use Proovit\LaravelProovit\ProovitClient;

$client = app(ProovitClient::class);

$balance = $client->tokens()->balance();
$reservation = $client->tokens()->reserve();
```

## Why it matters

- categories and folders can be used to build dynamic proof creation forms
- token balance lets your UI surface the available capacity for the current company
- token reservation should happen before proof initialization when the API requires it

## Notes

- reference data is intentionally lightweight and can be cached by the consumer application
- the SDK returns normalized DTOs for token balance and reservation, and option arrays for category/folder selects
