<?php

namespace App\Models;

use App\Enums\EventLookup;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentationPrescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = null;

    protected $hidden = ['event_type'];

    protected $appends = ['event_name'];

    public function prescriptionable()
    {
        return $this->morphTo();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function event()
    {
        return $this->morphTo();
    }

    public function __toString()
    {
        return "{$this->name} {$this->route} {$this->dosage} {$this->frequency} - for {$this->duration} " . (is_numeric($this->duration) ? 'days' : '');
    }

    public function eventName(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => EventLookup::tryFrom($attributes['event_type'])?->name ?? 'unknown_event', //"{$attributes['event_type']} #{$attributes['event_id']}"
        );
    }

    protected static function booted()
    {
        static::created(function (Self $self) {});
    }
}
