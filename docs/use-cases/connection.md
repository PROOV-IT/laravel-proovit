# Connection flow

The SDK is designed around a simple ProovIT authentication flow:

1. authenticate with email and password
2. receive a bearer token
3. fetch the list of companies available to the user
4. store the session data in the package settings store
5. select the company UUID to scope the following requests

## Base URL

Use the ProovIT API base URL, including the `/api` prefix.

Examples:

- `https://api.proov-it.online/api`
- `https://api.staging.proov-it.online/api`

## Headers

The client sends the following headers when they are available:

- `Authorization: Bearer <token>`
- `X-COMPANY-TOKEN: <company-uuid>`
- `X-COMPANY-ID: <company-uuid>`

The selected company UUID is stored under `connection.selected_company_uuid`. `connection.workspace_token` remains available as a compatibility alias.

## Login response

The login endpoint returns a bearer token. The package uses that token to fetch the list of companies and persist the session for later requests.

## Example

```php
$client = app(\Proovit\LaravelProovit\ProovitClient::class);

$session = $client->connection()->authenticate(
    email: 'admin@example.test',
    password: 'secret',
);
```

The returned payload contains the bearer token and the companies that can be bound to the current session.
