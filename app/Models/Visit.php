<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_type', 'visit_id', 'status', 'patient_id'
    ];

    protected $with = ['visit'];

    protected $appends = ['vital_staff'];

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
}
