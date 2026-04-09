<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\DTOs;

use Illuminate\Support\Arr;

final readonly class ProofTemplateCustomFieldData
{
    /**
     * @param  array<string, mixed>  $raw
     * @param  array<int, string> | string | null  $options
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $type,
        public bool $required = false,
        public array|string|null $options = null,
        public array $raw = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            key: (string) ($data['key'] ?? ''),
            label: (string) ($data['label'] ?? ($data['key'] ?? '')),
            type: (string) ($data['type'] ?? 'text'),
            required: (bool) ($data['required'] ?? false),
            options: Arr::get($data, 'options'),
            raw: $data,
        );
    }

    /**
     * @return array<int, string>
     */
    public function optionList(): array
    {
        if (is_array($this->options)) {
            return array_values(array_filter(array_map('strval', $this->options)));
        }

        if (is_string($this->options) && $this->options !== '') {
            return array_values(array_filter(array_map(
                static fn (string $option): string => trim($option),
                preg_split('/\s*,\s*/', $this->options) ?: [$this->options],
            )));
        }

        return [];
    }

    public function hasOptions(): bool
    {
        return $this->optionList() !== [];
    }
}
