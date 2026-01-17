<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CastsStatus;
use App\Traits\NeedsRecorderInfo;
use Illuminate\Database\Eloquent\Model;

class TreatmentPlan extends Model
{
    use NeedsRecorderInfo, CastsStatus;

    protected $fillable = [
        'patient_id',
        'user_id',
        'plan',
        'status',
        'origin_type',
        'origin_id',
    ];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function origin() {
        return $this->morphTo();
    }

    public function __toString()
    {
        return $this->plan;
    }
}
