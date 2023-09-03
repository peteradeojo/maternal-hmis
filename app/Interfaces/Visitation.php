<?php

namespace App\Interfaces;

interface Visitation
{
    public function lab();
    public function pharmacy();
    public function getType(): string;
}
