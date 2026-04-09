<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Builders\Proofs;

final class ProofMetadataBuilder
{
    /**
     * @var array<int, string>
     */
    private array $shareEmails = [];

    /**
     * @var array<int, string>
     */
    private array $keywords = [];

    /**
     * @var array<string, mixed>
     */
    private array $customFields = [];

    /**
     * @var array<string, mixed>|null
     */
    private ?array $signatureContext = null;

    private ?bool $isAnonymous = null;

    private ?string $location = null;

    private ?float $lat = null;

    private ?float $lng = null;

    public function withShareEmails(array $emails): self
    {
        $this->shareEmails = array_values(array_map('strval', $emails));

        return $this;
    }

    public function addShareEmail(string $email): self
    {
        $this->shareEmails[] = $email;

        return $this;
    }

    public function withAnonymous(bool $anonymous = true): self
    {
        $this->isAnonymous = $anonymous;

        return $this;
    }

    public function withKeywords(array $keywords): self
    {
        $this->keywords = array_values(array_map('strval', $keywords));

        return $this;
    }

    public function addKeyword(string $keyword): self
    {
        $this->keywords[] = $keyword;

        return $this;
    }

    public function withLocation(string $location, ?float $lat = null, ?float $lng = null): self
    {
        $this->location = $location;
        $this->lat = $lat;
        $this->lng = $lng;

        return $this;
    }

    public function withCustomField(string $key, mixed $value): self
    {
        $this->customFields[$key] = $value;

        return $this;
    }

    public function withCustomFields(array $customFields): self
    {
        foreach ($customFields as $key => $value) {
            $this->customFields[(string) $key] = $value;
        }

        return $this;
    }

    /**
     * @param  array<string, mixed>  $signatureContext
     */
    public function withSignatureContext(array $signatureContext): self
    {
        $this->signatureContext = $signatureContext;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $metadata = [
            'share_emails' => $this->shareEmails,
            'is_anonymous' => $this->isAnonymous,
            'keywords' => $this->keywords,
            'location' => $this->location,
            'custom_fields' => $this->customFields,
        ];

        if ($this->lat !== null) {
            $metadata['lat'] = $this->lat;
        }

        if ($this->lng !== null) {
            $metadata['lng'] = $this->lng;
        }

        if ($this->signatureContext !== null) {
            $metadata['signature_context'] = $this->signatureContext;
        }

        return array_filter(
            $metadata,
            static fn (mixed $value): bool => $value !== null
        );
    }
}
