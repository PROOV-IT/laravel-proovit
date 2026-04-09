# Proof builder

The proof builder is the recommended way to assemble a proof lifecycle payload when you need
to create a proof, upload its files, and optionally submit a signature payload.

## Example

```php
use Proovit\LaravelProovit\Builders\Proofs\ProofBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofMetadataBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofSignatureBuilder;
use Proovit\LaravelProovit\ProovitClient;

$client = app(ProovitClient::class)->withAccessToken($token);

$proofBuilder = $client->proofBuilder()
    ->withName('Contract proof')
    ->withDescription('Deposit a signed contract')
    ->withFolderId('019b463c-f588-7085-8528-e08275c483d8')
    ->withProofTemplateId('3aa73c80-6caa-44cd-a62c-e52913512f32')
    ->withTokenReservationId('019d72c6-4b75-7149-b94c-f7ddfb58df02')
    ->withMetadata(static function (ProofMetadataBuilder $metadata): void {
        $metadata
            ->withShareEmails(['jane.doe@example.com'])
            ->withAnonymous(false)
            ->withLocation('43.7265, 5.5434', 43.7265, 5.5434)
            ->withCustomFields([
                'material' => 'custom field 1 value required',
                'quantity' => 2,
                'delivery_date' => '2026-04-09',
            ]);
    })
    ->withFiles(static function (\Proovit\LaravelProovit\Builders\Proofs\ProofFilesBuilder $files): void {
        $files
            ->addFileFromPath(storage_path('app/proofs/contract.pdf'), 'contract.pdf', 'application/pdf')
            ->addFileFromContents('binary content', 'attachment.txt', 'text/plain');
    })
    ->withSignature(static function (ProofSignatureBuilder $signature): void {
        $signature
            ->withSignatureBase64($signatureBase64)
            ->withClientContext([
                'userAgent' => request()->userAgent(),
                'language' => app()->getLocale(),
            ]);
    });

$proof = $client->proofs()->init($proofBuilder);
$client->proofs()->uploadFiles($proof->id, $proofBuilder);

if ($proofBuilder->hasSignature()) {
    $client->proofs()->sign($proof->id, $proofBuilder);
}
```

## Payloads generated

- `toInitPayload()` for the `init` endpoint
- `toMultipart()` for the `files` endpoint
- `toSignPayload()` for the `sign` endpoint

## Notes

- Metadata is only sent when you configure it explicitly.
- Signature signing is optional and should only be triggered when the proof model requires it.
- The file builder computes the client hash and total bytes used by the API.
