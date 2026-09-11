<?php

namespace App\Services;

use App\Models\Location;

class LocationContext
{
    protected ?Location $location = null;

    public function set(Location $location): void
    {
        $this->location = $location;
    }

    public function get(): ?Location
    {
        return $this->location;
    }

    public function id(): ?int
    {
        return $this->location?->id;
    }

    public function has(): bool
    {
        return $this->location !== null;
    }
}
