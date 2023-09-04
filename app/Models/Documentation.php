<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'patient_id',
        'user_id',
        'symptoms',
        'prognosis',
        'comment',
        'status',
    ];

    protected $with = ['patient', 'visit', 'tests'];

    public function tests() {
        return $this->hasMany(DocumentationTest::class)->latest();
    }

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function visit() {
        return $this->belongsTo(Visit::class);
    }

    public function treatments() {
        return $this->hasMany(DocumentationPrescription::class)->latest();
    }
}
