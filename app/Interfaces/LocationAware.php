<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface LocationAware
{
    public function location(): BelongsTo;
}
