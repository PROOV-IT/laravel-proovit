<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Proovit\LaravelProovit\Models\ProovitSetting;
use Throwable;

final class ProovitSettingsRepository
{
    private const DEFAULT_KEY = 'default';

    public function all(): array
    {
        if (! $this->isAvailable()) {
            return [];
        }

        try {
            $payload = ProovitSetting::query()
                ->where('key', self::DEFAULT_KEY)
                ->value('payload');

            if (! is_string($payload) || $payload === '') {
                return [];
            }

            $decoded = json_decode(Crypt::decryptString($payload), true, 512, JSON_THROW_ON_ERROR);

            return is_array($decoded) ? $decoded : [];
        } catch (Throwable) {
            return [];
        }
    }

    public function save(array $settings): bool
    {
        if (! $this->isAvailable()) {
            return false;
        }

        try {
            ProovitSetting::query()->updateOrCreate(
                ['key' => self::DEFAULT_KEY],
                ['payload' => Crypt::encryptString(json_encode($this->prune($settings), JSON_THROW_ON_ERROR))],
            );

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function isAvailable(): bool
    {
        try {
            return Schema::hasTable('proovit_settings');
        } catch (Throwable) {
            return false;
        }
    }

    private function prune(array $settings): array
    {
        $filtered = [];

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $nested = $this->prune($value);

                if ($nested !== []) {
                    $filtered[$key] = $nested;
                }

                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $filtered[$key] = $value;
        }

        return $filtered;
    }
}
