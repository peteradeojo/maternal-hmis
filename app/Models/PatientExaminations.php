<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientExaminations extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'documenation_id',
        'general',
        'specifics',
    ];

    protected $casts = [
        'specifics' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->morphTo();
    }
}
