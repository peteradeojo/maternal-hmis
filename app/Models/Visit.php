<?php

namespace App\Models;

use App\Dto\PrescriptionDto;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property AncVisit|GeneralVisit $visit
 */
class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_type',
        'visit_id',
        'status',
        'patient_id',
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
        return $this->morphOne(Vitals::class, 'recordable');
    }

    // public function notes()
    // {
    //     return $this->morphMany(ConsultationNote::class, 'visit');
    // }

    // public function diagnoses()
    // {
    //     return $this->morphMany(DocumentedDiagnosis::class, 'diagnosable');
    // }

    // public function tests()
    // {
    //     return $this->morphMany(DocumentationTest::class, 'testable');
    // }

    // public function prescriptions()
    // {
    //     return $this->morphMany(DocumentationPrescription::class, 'event');
    // }

    public function addPrescription(Patient $patient, Product $product, PrescriptionDto $data, $event)
    {
        return $this->visit->prescriptions()->create([
            'patient_id' => $patient->id,
            'prescriptionable_type' => $product::class,
            'prescriptionable_id' => $product->id,
            'name' => $product->name,
            'dosage' => $data->dosage,
            'duration' => $data->duration,
            'route' => $data->route,
            'frequency' => $data->frequency,
            'requested_by' => auth()->user()?->id,
            'event_type' => $event::class,
            'event_id' => $event->id,
        ]);
    }

    // public function imagings()
    // {
    //     return $this->morphMany(PatientImaging::class, 'documentable');
    // }

    public function getWaitingForDoctorAttribute() {
        return ($this->visit->tests()->pending()->exists() or $this->visit->radios()->status(Status::pending)->exists()) and $this->status != Status::closed->value;
    }
}
