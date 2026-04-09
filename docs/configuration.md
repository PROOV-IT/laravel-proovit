# Configuration

`config/proovit.php` exposes the connection profile used by the SDK.

The SDK resolves configuration in this order:

1. the persisted ProovIT configuration stored in the database
2. the package config file, which is usually backed by environment variables

This means saved values override the matching `.env` values without requiring you to edit the environment again.

## Connection keys

- `connection.base_url`
- `connection.app_url`
- `connection.company_name`
- `connection.login_email`
- `connection.api_key`
- `connection.access_token`
- `connection.selected_company_uuid`
- `connection.workspace_token`
- `connection.mode`
- `connection.timeout`
- `connection.connect_timeout`
- `connection.verify_tls`
- `connection.retry_attempts`
- `connection.retry_sleep_ms`
- `connection.health_endpoint`

## Persistence

The package stores a single encrypted settings payload in `proovit_settings`.

If the table does not exist yet, the SDK falls back to the `.env` values and keeps working.

`connection.selected_company_uuid` is the canonical key for the active company. `connection.workspace_token` is kept as a backward-compatible alias for older installations.

### Save flow

- create or update the settings through the Filament settings page
- the stored payload is encrypted at rest
- on the next request, the SDK uses the database values first and the environment only as fallback
