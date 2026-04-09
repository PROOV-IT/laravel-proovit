<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Models;

use Illuminate\Database\Eloquent\Model;

final class ProovitSetting extends Model
{
    protected $table = 'proovit_settings';

    protected $guarded = [];
}
