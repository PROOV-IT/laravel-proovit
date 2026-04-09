<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Enums;

enum ProovitMode: string
{
    case Production = 'production';
    case Staging = 'staging';
    case Sandbox = 'sandbox';
    case Local = 'local';
}
