<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Enums;

enum ProovitProofStatus: string
{
    case Draft = 'draft';
    case PendingFiles = 'pending_files';
    case PendingSignature = 'pending_signature';
    case Signed = 'signed';
    case Certified = 'certified';
    case Refused = 'refused';
    case Failed = 'failed';
}
