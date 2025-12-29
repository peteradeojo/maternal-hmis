<?php

namespace App\Interfaces;

interface OperationalEvent
{
    public function scopeActive($query);

    public function imagings();
    public function tests();
}
