<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Builders\Proofs;

final class ProofSignatureBuilder
{
    private ?string $signatureBase64 = null;

    /**
     * @var array<string, mixed>
     */
    private array $clientContext = [];

    public function withSignatureBase64(string $signatureBase64): self
    {
        $this->signatureBase64 = $signatureBase64;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $clientContext
     */
    public function withClientContext(array $clientContext): self
    {
        $this->clientContext = $clientContext;

        return $this;
    }

    public function withClientUserAgent(string $userAgent): self
    {
        $this->clientContext['userAgent'] = $userAgent;

        return $this;
    }

    public function withClientLanguage(string $language): self
    {
        $this->clientContext['language'] = $language;

        return $this;
    }

    public function withClientTimeZone(string $timeZone): self
    {
        $this->clientContext['timeZone'] = $timeZone;

        return $this;
    }

    public function withClientPageUrl(string $pageUrl): self
    {
        $this->clientContext['pageUrl'] = $pageUrl;

        return $this;
    }

    public function withClientSignedAt(string $clientSignedAt): self
    {
        $this->clientContext['clientSignedAt'] = $clientSignedAt;

        return $this;
    }

    public function withClientScreen(int $width, int $height, float $pixelRatio = 1.0): self
    {
        $this->clientContext['screen'] = [
            'width' => $width,
            'height' => $height,
            'pixelRatio' => $pixelRatio,
        ];

        return $this;
    }

    /**
     * @param  array<string, mixed>  $geolocation
     */
    public function withClientGeolocation(array $geolocation): self
    {
        $this->clientContext['geolocation'] = $geolocation;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->signatureBase64 !== null) {
            $payload['signature_base64'] = $this->signatureBase64;
        }

        if ($this->clientContext !== []) {
            $payload['client_context'] = $this->clientContext;
        }

        return $payload;
    }

    public function hasSignature(): bool
    {
        return $this->signatureBase64 !== null && $this->signatureBase64 !== '';
    }
}
