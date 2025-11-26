<?php

namespace App\Interfaces;

interface OperationalEvent
{
    public function scopeActive($query);
}
