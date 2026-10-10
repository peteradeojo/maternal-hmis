<?php

namespace App\Interfaces;

interface Documentable extends Imageable, Prescribable, Testable
{
    public function complaints();
}
