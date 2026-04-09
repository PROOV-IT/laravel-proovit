<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Builders\Proofs;

use InvalidArgumentException;

final class ProofFilesBuilder
{
    /**
     * @var array<int, ProofFileBuilder>
     */
    private array $files = [];

    public static function fromLegacyFiles(array $files): self
    {
        $builder = new self;

        foreach ($files as $file) {
            if (! is_array($file)) {
                throw new InvalidArgumentException('Each proof file payload must be an array.');
            }

            $builder->withFile(static function (ProofFileBuilder $fileBuilder) use ($file): void {
                $fileBuilder
                    ->withName((string) ($file['name'] ?? 'files[]'))
                    ->withContents($file['contents'] ?? '')
                    ->withFilename($file['filename'] ?? null)
                    ->withMimeType($file['mime_type'] ?? $file['mimeType'] ?? null)
                    ->withMetadataInjected((bool) ($file['metadata_injected'] ?? false));
            });
        }

        return $builder;
    }

    public function withFile(callable $callback): self
    {
        $file = new ProofFileBuilder;
        $callback($file);

        $this->files[] = $file;

        return $this;
    }

    public function addFile(ProofFileBuilder $file): self
    {
        $this->files[] = $file;

        return $this;
    }

    public function addFileFromContents(
        string $contents,
        ?string $filename = null,
        ?string $mimeType = null,
        bool $metadataInjected = false,
        string $name = 'files[]',
    ): self {
        $file = new ProofFileBuilder;
        $file->withContents($contents)
            ->withFilename($filename)
            ->withMimeType($mimeType)
            ->withMetadataInjected($metadataInjected)
            ->withName($name);

        return $this->addFile($file);
    }

    public function addFileFromPath(
        string $path,
        ?string $filename = null,
        ?string $mimeType = null,
        bool $metadataInjected = false,
        string $name = 'files[]',
    ): self {
        $contents = fopen($path, 'rb');
        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Unable to open proof file path "%s".', $path));
        }

        return $this->addFile(
            (new ProofFileBuilder)
                ->withContents($contents)
                ->withFilename($filename ?? basename($path))
                ->withMimeType($mimeType)
                ->withMetadataInjected($metadataInjected)
                ->withName($name)
        );
    }

    public function isEmpty(): bool
    {
        return $this->files === [];
    }

    public function toMultipart(): array
    {
        $multipart = [];
        $totalBytes = 0;

        foreach ($this->files as $file) {
            $fileData = $file->toArray();

            $multipart[] = [
                'name' => $fileData['name'],
                'contents' => $fileData['contents'],
                'filename' => $fileData['filename'],
                'headers' => array_filter([
                    'Content-Type' => $fileData['mime_type'],
                ], static fn (mixed $value): bool => $value !== null && $value !== ''),
            ];

            $multipart[] = [
                'name' => 'client_hashes[]',
                'contents' => $file->clientHash(),
            ];

            $multipart[] = [
                'name' => 'metadata_injected[]',
                'contents' => $fileData['metadata_injected'] ? '1' : '0',
            ];

            $totalBytes += $file->byteSize();
        }

        $multipart[] = [
            'name' => 'client_total_bytes',
            'contents' => (string) $totalBytes,
        ];

        return $multipart;
    }
}
