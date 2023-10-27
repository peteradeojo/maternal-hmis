<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\Relation;

interface Documentable extends Testable, Prescribable, Imageable
{
    public function complaints();
}
