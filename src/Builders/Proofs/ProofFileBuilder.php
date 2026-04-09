<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Builders\Proofs;

use InvalidArgumentException;

final class ProofFileBuilder
{
    private mixed $contents = '';

    private string $name = 'files[]';

    private ?string $filename = null;

    private ?string $mimeType = null;

    private bool $metadataInjected = false;

    public function withContents(mixed $contents): self
    {
        $this->contents = $contents;

        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function withMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function withMetadataInjected(bool $metadataInjected = true): self
    {
        $this->metadataInjected = $metadataInjected;

        return $this;
    }

    public function toMultipartParts(): array
    {
        $contents = $this->contents;

        if (is_string($contents) && $contents === '') {
            throw new InvalidArgumentException('A proof file contents value is required.');
        }

        return [
            [
                'name' => $this->name,
                'contents' => $contents,
                'filename' => $this->filename,
                'headers' => array_filter([
                    'Content-Type' => $this->mimeType,
                ], static fn (mixed $value): bool => $value !== null && $value !== ''),
            ],
            [
                'name' => 'client_hashes[]',
                'contents' => $this->clientHash(),
            ],
            [
                'name' => 'metadata_injected[]',
                'contents' => $this->metadataInjected ? '1' : '0',
            ],
        ];
    }

    public function byteSize(): int
    {
        $contents = $this->contents;

        if (is_string($contents)) {
            return strlen($contents);
        }

        if (is_resource($contents)) {
            $position = ftell($contents);
            if ($position === false) {
                throw new InvalidArgumentException('Unable to read the proof file contents stream.');
            }

            $hashContext = hash_init('sha256');
            $size = hash_update_stream($hashContext, $contents);
            if ($size === false) {
                throw new InvalidArgumentException('Unable to read the proof file contents stream.');
            }

            if (stream_get_meta_data($contents)['seekable'] ?? false) {
                rewind($contents);
            }

            return $size;
        }

        if (is_object($contents) && method_exists($contents, '__toString')) {
            return strlen((string) $contents);
        }

        throw new InvalidArgumentException('Unsupported proof file contents type.');
    }

    public function clientHash(): string
    {
        $contents = $this->contents;

        if (is_string($contents)) {
            return hash('sha256', $contents);
        }

        if (is_resource($contents)) {
            $hashContext = hash_init('sha256');
            $result = hash_update_stream($hashContext, $contents);
            if ($result === false) {
                throw new InvalidArgumentException('Unable to hash the proof file contents stream.');
            }

            $hash = hash_final($hashContext);

            if (stream_get_meta_data($contents)['seekable'] ?? false) {
                rewind($contents);
            }

            return $hash;
        }

        if (is_object($contents) && method_exists($contents, '__toString')) {
            return hash('sha256', (string) $contents);
        }

        throw new InvalidArgumentException('Unsupported proof file contents type.');
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'contents' => $this->contents,
            'filename' => $this->filename,
            'mime_type' => $this->mimeType,
            'metadata_injected' => $this->metadataInjected,
            'client_hash' => $this->clientHash(),
            'client_total_bytes' => $this->byteSize(),
        ];
    }
}
