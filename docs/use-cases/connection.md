# Connection flow

The SDK is designed around a simple ProovIT authentication flow:

1. authenticate with email and password
2. receive a bearer token
3. fetch the list of companies available to the user
4. persist the session data in the package settings store
5. select the company UUID to scope the following requests
6. refresh the bearer later when the stored session expires or is revoked

For application code, the easiest path is:

1. authenticate the connection
2. persist the returned session
3. select the company UUID once the user has picked one
4. reuse the saved session on later requests
5. call `refreshBearer()` when the API starts rejecting the current bearer

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

## Refreshing the bearer

When the stored bearer token becomes invalid, you can refresh it from the saved login credentials:

```php
$client = app(\Proovit\LaravelProovit\ProovitClient::class);
$connection = $client->connection()->refreshBearer();
```

The helper reuses the persisted login email and password, requests a new bearer token, and keeps the selected company UUID when possible.

## Login response

The login endpoint returns a bearer token. The package uses that token to fetch the list of companies and persist the session for later requests.

## Example

```php
$client = app(\Proovit\LaravelProovit\ProovitClient::class);

$session = $client->connection()->authenticate(
    email: 'admin@example.test',
    password: 'secret',
);

$client->connection()->persist($session);
```

When the user selects a company later, persist the selected UUID too:

```php
$client->connection()->persist($session, '01985517-991d-714f-b375-9cf8e534a843');
```

You can also combine both steps:

```php
$client->connection()->authenticateAndPersist(
    email: 'admin@example.test',
    password: 'secret',
    selectedCompanyUuid: '01985517-991d-714f-b375-9cf8e534a843',
);
```

The returned payload contains the bearer token and the companies that can be bound to the current session.

## Convenience helpers

The connection resource also exposes:

- `persist(ProovitConnectionData|array $connection, ?string $selectedCompanyUuid = null)`
- `selectCompany(string $companyUuid)`
- `authenticateAndPersist(string $email, string $password, ?string $selectedCompanyUuid = null)`

These helpers are the recommended way to manage the session when your app wants to separate login from company selection.
