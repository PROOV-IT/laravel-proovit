# Proof lifecycle

```php
$client = app(\Proovit\LaravelProovit\ProovitClient::class)->withAccessToken($token);

$proofBuilder = $client->proofBuilder()
    ->withName('Contract proof')
    ->withDescription('Deposit a signed contract')
    ->withFolderId('019b463c-f588-7085-8528-e08275c483d8')
    ->withTokenReservationId('019d72c6-4b75-7149-b94c-f7ddfb58df02')
    ->withProofTemplateId('3aa73c80-6caa-44cd-a62c-e52913512f32')
    ->withFiles(static function (\Proovit\LaravelProovit\Builders\Proofs\ProofFilesBuilder $files): void {
        $files->addFileFromPath(storage_path('app/contract.pdf'), 'contract.pdf', 'application/pdf');
    });

$proof = $client->proofs()->init($proofBuilder);

$client->proofs()->uploadFiles($proof->id, $proofBuilder);
```

For complex proofs with custom metadata or signing context, see [Proof builder](proof-builder.md).
