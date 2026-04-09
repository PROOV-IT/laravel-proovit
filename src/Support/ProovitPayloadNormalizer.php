<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Support;

final class ProovitPayloadNormalizer
{
    public function normalizeFiles(array $files): array
    {
        return array_map(static function (array $file): array {
            return [
                'name' => (string) ($file['name'] ?? 'files[]'),
                'contents' => $file['contents'] ?? '',
                'filename' => $file['filename'] ?? null,
                'headers' => array_filter([
                    'Content-Type' => $file['mime_type'] ?? $file['mimeType'] ?? null,
                ]),
            ];
        }, $files);
    }
}
