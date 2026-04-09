<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Builders\Proofs;

use InvalidArgumentException;

final class ProofBuilder
{
    private ?string $name = null;

    private ?string $description = null;

    private ?string $folderId = null;

    private ?string $categoryId = null;

    private ?string $tokenReservationId = null;

    private ?string $proofTemplateId = null;

    private bool $metadataConfigured = false;

    private bool $signatureConfigured = false;

    private ?ProofMetadataBuilder $metadataBuilder = null;

    private ?ProofFilesBuilder $filesBuilder = null;

    private ?ProofSignatureBuilder $signatureBuilder = null;

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function withFolderId(string $folderId): self
    {
        $this->folderId = $folderId;

        return $this;
    }

    public function withCategoryId(?string $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function withTokenReservationId(string $tokenReservationId): self
    {
        $this->tokenReservationId = $tokenReservationId;

        return $this;
    }

    public function withProofTemplateId(string $proofTemplateId): self
    {
        $this->proofTemplateId = $proofTemplateId;

        return $this;
    }

    /**
     * @param  callable(ProofMetadataBuilder):void|array<string, mixed>|null  $callback
     */
    public function withMetadata(callable|array|null $callback = null): self
    {
        $this->metadataConfigured = true;
        $this->metadataBuilder ??= new ProofMetadataBuilder;

        if (is_array($callback)) {
            $this->metadataBuilder->withShareEmails((array) ($callback['share_emails'] ?? []))
                ->withKeywords((array) ($callback['keywords'] ?? []))
                ->withCustomFields((array) ($callback['custom_fields'] ?? []));

            if (array_key_exists('is_anonymous', $callback)) {
                $this->metadataBuilder->withAnonymous((bool) $callback['is_anonymous']);
            }

            if (array_key_exists('location', $callback)) {
                $this->metadataBuilder->withLocation(
                    (string) $callback['location'],
                    isset($callback['lat']) ? (float) $callback['lat'] : null,
                    isset($callback['lng']) ? (float) $callback['lng'] : null,
                );
            }

            if (isset($callback['signature_context']) && is_array($callback['signature_context'])) {
                $this->metadataBuilder->withSignatureContext($callback['signature_context']);
            }

            return $this;
        }

        if (is_callable($callback)) {
            $callback($this->metadataBuilder);
        }

        return $this;
    }

    /**
     * @param  callable(ProofFilesBuilder):void|array<int, array<string, mixed>>|null  $callback
     */
    public function withFiles(callable|array|null $callback = null): self
    {
        $this->filesBuilder ??= new ProofFilesBuilder;

        if (is_array($callback)) {
            $this->filesBuilder = ProofFilesBuilder::fromLegacyFiles($callback);

            return $this;
        }

        if (is_callable($callback)) {
            $callback($this->filesBuilder);
        }

        return $this;
    }

    /**
     * @param  callable(ProofSignatureBuilder):void|array<string, mixed>|null  $callback
     */
    public function withSignature(callable|array|null $callback = null): self
    {
        $this->signatureConfigured = true;
        $this->signatureBuilder ??= new ProofSignatureBuilder;

        if (is_array($callback)) {
            if (isset($callback['signature_base64'])) {
                $this->signatureBuilder->withSignatureBase64((string) $callback['signature_base64']);
            }

            if (isset($callback['client_context']) && is_array($callback['client_context'])) {
                $this->signatureBuilder->withClientContext($callback['client_context']);
            }

            return $this;
        }

        if (is_callable($callback)) {
            $callback($this->signatureBuilder);
        }

        return $this;
    }

    public function withoutSignature(): self
    {
        $this->signatureConfigured = false;
        $this->signatureBuilder = null;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toInitPayload(): array
    {
        $payload = array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'folder_id' => $this->folderId,
            'category_id' => $this->categoryId,
            'token_reservation_id' => $this->tokenReservationId,
            'proof_template_id' => $this->proofTemplateId,
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        if ($this->metadataConfigured && $this->metadataBuilder !== null) {
            $payload['metadata'] = $this->metadataBuilder->toArray();
        }

        return $payload;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toMultipart(): array
    {
        if ($this->filesBuilder === null || $this->filesBuilder->isEmpty()) {
            throw new InvalidArgumentException('At least one proof file is required.');
        }

        return $this->filesBuilder->toMultipart();
    }

    /**
     * @return array<string, mixed>
     */
    public function toSignPayload(): array
    {
        if (! $this->signatureConfigured || $this->signatureBuilder === null) {
            return [];
        }

        return $this->signatureBuilder->toArray();
    }

    public function hasSignature(): bool
    {
        return $this->signatureConfigured && $this->signatureBuilder?->hasSignature() === true;
    }

    public function hasFiles(): bool
    {
        return $this->filesBuilder !== null && ! $this->filesBuilder->isEmpty();
    }

    public function metadata(): ProofMetadataBuilder
    {
        $this->metadataConfigured = true;
        $this->metadataBuilder ??= new ProofMetadataBuilder;

        return $this->metadataBuilder;
    }

    public function files(): ProofFilesBuilder
    {
        $this->filesBuilder ??= new ProofFilesBuilder;

        return $this->filesBuilder;
    }

    public function signature(): ProofSignatureBuilder
    {
        $this->signatureConfigured = true;
        $this->signatureBuilder ??= new ProofSignatureBuilder;

        return $this->signatureBuilder;
    }
}
