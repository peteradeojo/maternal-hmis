<?php

namespace App\Traits;

use App\Events\BillableCreated;
use App\Events\BillableDeleted;
use App\Events\BillableUpdated;
use App\Models\Visit;

trait Billable
{
    static function bootsBillable()
    {
        static::created(function (Self $self) {
            logger()->debug("Billable item created: " . $self::class);
            event(new BillableCreated($self, auth()->user()->id));
        });

        static::updated(function (Self $self) {
            logger()->debug("Billable item updated: " . $self::class);
            event(new BillableUpdated($self));
        });

        static::created(function (Self $self) {
            logger()->debug("Billable item deleted: " . $self::class);
            event(new BillableDeleted($self));
        });
    }
}
