<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Support;

use Proovit\LaravelProovit\Enums\ProovitExportStatus;
use Proovit\LaravelProovit\Enums\ProovitProofStatus;

final class ProovitStatusMapper
{
    public static function toProofStatus(string $status): ProovitProofStatus
    {
        return match ($status) {
            'draft', 'init' => ProovitProofStatus::Draft,
            'pending_files', 'files_pending', 'uploading' => ProovitProofStatus::PendingFiles,
            'pending_signature', 'waiting_signature', 'awaiting_signatures' => ProovitProofStatus::PendingSignature,
            'signed', 'validated', 'finalized' => ProovitProofStatus::Signed,
            'certified', 'anchored', 'verified', 'on_chain' => ProovitProofStatus::Certified,
            'refused', 'rejected', 'cancelled' => ProovitProofStatus::Refused,
            default => ProovitProofStatus::Failed,
        };
    }

    public static function toExportStatus(string $status): ProovitExportStatus
    {
        return match ($status) {
            'draft', 'init', 'pending', 'processing', 'uploading' => ProovitExportStatus::Processing,
            'pending_signature', 'awaiting_signatures', 'partially_signed' => ProovitExportStatus::ManualActionRequired,
            'signed', 'certified', 'anchored', 'verified', 'finalized', 'on_chain' => ProovitExportStatus::Succeeded,
            default => ProovitExportStatus::Failed,
        };
    }
}
