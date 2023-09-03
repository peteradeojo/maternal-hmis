<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntenatalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'lmp', 'edd', 'spouse_name', 'spouse_phone', 'spouse_occupation', 'spouse_educational_status', 'gravida', 'parity', 'card_type'
    ];

    protected $casts = [
        'lmp' => 'date',
        'edd' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function history()
    {
        return $this->hasMany(AncVisit::class)->whereNot('doctor_id', null);
    }
}
