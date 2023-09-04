<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_type', 'visit_id', 'status', 'patient_id'
    ];

    protected $with = ['visit'];

    protected $appends = ['vital_staff', 'can_check_out'];

    protected $casts = [
        'vitals' => 'object',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->morphTo();
    }

    public function getVisitType()
    {
        return $this->visit->getType();
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
}
