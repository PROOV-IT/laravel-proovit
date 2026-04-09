# Events

`proovit/laravel-proovit` dispatches domain events after the SDK completes a successful API action.
These events are useful when you want to synchronize audit logs, local state, notifications, or side effects in your own Laravel app.

## Available events

- `Proovit\LaravelProovit\Events\Connection\ConnectionAuthenticated`
- `Proovit\LaravelProovit\Events\Tokens\TokenReserved`
- `Proovit\LaravelProovit\Events\Proofs\ProofInitialized`
- `Proovit\LaravelProovit\Events\Proofs\ProofFilesUploaded`
- `Proovit\LaravelProovit\Events\Proofs\ProofSigned`
- `Proovit\LaravelProovit\Events\Proofs\ProofRevoked`
- `Proovit\LaravelProovit\Events\Proofs\ProofDeleted`

## What they represent

- `ConnectionAuthenticated` fires after the SDK successfully logs in and retrieves the company list.
- `TokenReserved` fires after the SDK reserves a token for a proof deposit flow.
- `ProofInitialized` fires after `init` succeeds.
- `ProofFilesUploaded` fires after files are uploaded to a proof.
- `ProofSigned` fires after a signature payload is submitted.
- `ProofRevoked` fires after a proof is revoked.
- `ProofDeleted` fires after a proof is deleted.

## Example listener

```php
use Illuminate\Support\Facades\Event;
use Proovit\LaravelProovit\Events\Connection\ConnectionAuthenticated;
use Proovit\LaravelProovit\Events\Proofs\ProofInitialized;

Event::listen(ConnectionAuthenticated::class, static function (ConnectionAuthenticated $event): void {
    logger()->info('ProovIT connection refreshed', [
        'login' => $event->connection->loginEmail,
        'companies_count' => count($event->connection->companies),
    ]);
});

Event::listen(ProofInitialized::class, static function (ProofInitialized $event): void {
    logger()->info('ProovIT proof initialized', [
        'proof_id' => $event->proof->id,
        'status' => $event->proof->status,
    ]);
});
```

## Typical use cases

- store a local audit trail when a proof is created or revoked
- refresh a cache when the connection or company list changes
- notify another subsystem when a proof is signed
- keep a local analytics table in sync with the proof lifecycle

## Notes

- the events are dispatched synchronously by the SDK
- payload objects are normalized DTOs or simple arrays, depending on the event
- if you do not listen to an event, it has no side effect
