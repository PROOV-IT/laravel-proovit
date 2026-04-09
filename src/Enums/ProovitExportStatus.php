<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Enums;

enum ProovitExportStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case ManualActionRequired = 'manual_action_required';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
}
