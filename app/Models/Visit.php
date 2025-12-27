<?php

namespace App\Models;

use App\Enums\Department;
use App\Enums\Status;
use App\Http\Controllers\LabController;
use App\Interfaces\OperationalEvent;
use App\Traits\Documentable;
use App\Traits\HasVisitData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property AncVisit|GeneralVisit $visit
 */
class Visit extends Model implements OperationalEvent
{
    use HasFactory, HasVisitData, Documentable, Auditable;

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

    protected $appends = ['can_check_out', 'type'];

    protected $casts = [];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->morphTo();
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    // Methods
    public function getReadableVisitTypeAttribute()
    {
        return $this->visit?->getType();
    }

    public function type(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->visit->type,
        );
    }

    public function canCheckOut(): Attribute
    {
        return Attribute::make(fn() => !$this->awaiting_pharmacy && !$this->awaiting_doctor);
    }

    // Scopes
    public function scopeAwaitingDoctor($query)
    {
        $query->has('vitals')->where('awaiting_doctor', true);
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

    public function scopeActive($query)
    {
        $query->where('status', Status::active->value)->latest();
    }

    public function scopeAccessibleBy($query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole('doctor')) {
            return $query->where('consultant_id', $user->id);
        }

        if ($user->hasRole('nurse')) {
            return $query->where(function ($q) {
                $q->where('awaiting_vitals', true)->orWhere('awaiting_doctor', true);
            });
        }

        if ($user->hasRole('lab')) {
            return $query->where('awaiting_lab_results', true);
        }

        if ($user->hasRole('radiology')) {
            return $query->where('awaiting_radiology', true);
        }

        if ($user->hasRole('pharmacy')) {
            return $query->where('awaiting_pharmacy', true);
        }

        if ($user->hasAnyRole(['billing', 'record'])) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
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
        return $this->morphMany(Vitals::class, 'recordable');
    }

    public function getWaitingForDoctorAttribute()
    {
        $b = ($this->visit->tests()->where('results', '!=', null)->pending()->exists() or $this->visit->radios()->status(Status::pending)->exists()) and $this->status != Status::closed->value;
        return $b;
    }

    public function bills()
    {
        return $this->morphMany(Bill::class, 'billable')->latest();
    }

    public function admission()
    {
        return $this->hasOne(Admission::class);
    }

    public function active_bills()
    {
        return $this->bills()->where('status', '!=', Status::cancelled->value);
    }

    protected static function booted()
    {
        static::created(function (self $visit) {
            if ($visit->visit_type == AncVisit::class) {
                $tests = Product::whereIn('name', LabController::$ancFollowupTests)->orderByDesc('amount')->get()->unique('name');
                if ($tests->isEmpty()) {
                    return;
                }

                foreach ($tests as $test) {
                    if ($visit->tests()->where('name', $test->name)->exists())
                        continue;

                    $visit->tests()->create([
                        'name' => $test->name,
                        'describable_type' => $test::class,
                        'describable_id' => $test->id,
                        'patient_id' => $visit->patient->id,
                    ]);
                }

                notifyDepartment(Department::LAB->value, "Antenatal visit started for {$visit->patient->name}", ['timeout' => 10000]);
            }
        });
    }
}
