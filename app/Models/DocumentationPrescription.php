<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationPrescription extends Model
{
    use HasFactory;

    protected $guarded = null;

    public function prescriptionable()
    {
        return $this->morphTo();
    }

    public function patient() {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function __toString()
    {
        return "{$this->name} {$this->route} {$this->dosage} {$this->route}";
    }
}
