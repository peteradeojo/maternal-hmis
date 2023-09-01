<?php

namespace App\Interfaces;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface Visitable
{
    // public function vitals(): HasOne & Builder;
    public function lab();
    public function pharmacy();
}
