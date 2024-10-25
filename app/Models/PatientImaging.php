<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientImaging extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function requester () {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Event: visit or admission
    public function documentable() {
        return $this->morphTo();
    }

    // The poduct details
    public function describable() {
        return $this->morphTo();
    }

    public function scopeStatus($query, Status $status) {
        $query->where('status', $status->value);
    }

    public function getSecurePathAttribute() {
        if (str_contains($this->path, 'cloudinary')) {
            return $this->path;
        }

        return asset($this->path);
    }

    public function __toString()
    {
        return $this->name;
    }
}
