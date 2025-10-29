<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\Documentable;
use App\Traits\HasVisitData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property AncVisit|GeneralVisit $visit
 */
class Visit extends Model
{
    use HasFactory, HasVisitData, Documentable;

    protected $fillable = [
        'visit_type',
        'visit_id',
        'status',
        'patient_id',
        'consultant_id',
        'awaiting_vitals',
        'awaiting_doctor',
        'awaiting_lab_results',
        'awaiting_radiology',
        'awaiting_tests',
        'awaiting_pharmacy',
    ];

    protected $with = ['visit'];

    protected $appends = ['vital_staff', 'can_check_out'];

    protected $casts = [
        'vitals' => 'object',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->morphTo();
    }

    // Methods
    public function getReadableVisitTypeAttribute()
    {
        return $this->visit?->getType();
    }

    public function setVitals($data, User $user)
    {
        $vitals = [
            'data' => $data,
            'staff' => $user->id,
            'time' => now(),
        ];
        $this->vitals = $vitals;
        $this->save();
    }

    public function getVitalStaffAttribute()
    {
        if (isset($this->vitals?->staff))
            return User::find($this->vitals?->staff);
    }

    public function canCheckOut(): Attribute
    {
        return Attribute::make(fn () =>  !$this->awaiting_pharmacy && !$this->awaiting_doctor);
    }

    // Scopes
    public function scopeAwaitingDoctor($query)
    {
        $query->whereNotNull('vitals')->where('awaiting_doctor', true);
    }

    public function scopeCompleted($query)
    {
        $query->where('status', Status::completed->value);
    }

    public function scopeAwaitingPharmacy($query)
    {
        $query->where('awaiting_pharmacy', true);
    }

    public function scopeAwaitingLab($query)
    {
        $query->where('awaiting_lab_results', true);
    }

    public function scopeAwaiting($query)
    {
        $query->where('awaiting_doctor', true)->orWhere('awaiting_vitals', true)->orWhere('awaiting_pharmacy', true);
    }

    public function checkOut($force = false)
    {
        if ($force) {
            $this->update([
                'status' => Status::closed->value,
                'awaiting_doctor' => false,
                'awaiting_vitals' => false,
            ]);
            return;
        }

        if ($this->can_check_out) {
            $this->update(['status' => Status::closed->value]);
        }
    }

    public function svitals()
    {
        return $this->vitals();
    }

    public function getWaitingForDoctorAttribute() {
        $b = ($this->visit->tests()->where('results', '!=', null)->pending()->exists() or $this->visit->radios()->status(Status::pending)->exists()) and $this->status != Status::closed->value;
        return $b;
    }
}
