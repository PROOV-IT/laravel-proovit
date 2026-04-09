# Persistent configuration

The ProovIT SDK keeps working with only environment variables, but you can persist a configuration profile in the database and let it override the `.env` defaults.

## What is stored

- `connection.base_url`
- `connection.app_url`
- `connection.company_name`
- `connection.login_email`
- `connection.api_key`
- `connection.access_token`
- `connection.workspace_token`
- `connection.mode`
- `connection.timeout`
- `connection.connect_timeout`
- `connection.verify_tls`
- `connection.retry_attempts`
- `connection.retry_sleep_ms`
- `connection.health_endpoint`
- API routing paths and feature flags
- certificate/export/audit/docs defaults

## Behavior

1. The package reads `config/proovit.php`.
2. It merges the encrypted persisted settings stored in `proovit_settings`.
3. The persisted values win over the matching config entries.
4. Missing settings still fall back to the environment.

## Practical impact

You can update the ProovIT endpoint, workspace credentials, company metadata and runtime defaults from the panel without redeploying or editing environment files.
