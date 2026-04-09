<?php

declare(strict_types=1);

use Proovit\LaravelProovit\DTOs\CategoryData;
use Proovit\LaravelProovit\DTOs\FolderData;
use Proovit\LaravelProovit\DTOs\TokenBalanceData;
use Proovit\LaravelProovit\DTOs\TokenReservationData;

it('parses category, folder and token reference payloads', function (): void {
    $category = CategoryData::fromArray([
        'id' => 'cat-1',
        'company_id' => 'company-1',
        'name' => 'Category',
        'slug' => 'category',
        'color' => '#fff',
        'metadata' => ['foo' => 'bar'],
        'is_active' => true,
        'is_shared' => false,
        'parent_id' => null,
    ]);

    $folder = FolderData::fromArray([
        'id' => 'folder-1',
        'company_id' => 'company-1',
        'name' => 'Folder',
        'slug' => 'folder',
        'parent_id' => null,
        'is_active' => true,
        'visibility_level' => 'company',
    ]);

    $balance = TokenBalanceData::fromArray([
        'balance' => 12,
        'company_id' => 'company-1',
        'user_id' => 7,
    ]);

    $reservation = TokenReservationData::fromArray([
        'reservation_id' => 'reservation-1',
        'status' => 'held',
    ]);

    expect($category->id)->toBe('cat-1')
        ->and($category->name)->toBe('Category')
        ->and($folder->slug)->toBe('folder')
        ->and($balance->balance)->toBe(12)
        ->and($reservation->reservationId)->toBe('reservation-1');
});
