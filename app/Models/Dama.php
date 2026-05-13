<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Dama extends Model
{
    use Auditable;
    //
    protected $fillable = [
        'admission_id',
        'patient_id',
        'user_id',
        'name',
        'patient_signature',
        'relationship',
        'relative',
        'relative_name',
        'relative_signature',
        'relative_relationship',
        'nurse', 'nurse_signature'
    ];

    public function patient_signature() {
        return $this->morphOne(SignatureImage::class, 'event');
    }

    public function relative_signature() {
        return $this->morphOne(SignatureImage::class, 'event');
    }

    public function nurse_signature() {
        return $this->morphOne(SignatureImage::class, 'event');
    }

    public function admission() {
        return $this->belongsTo(Admission::class);
    }
}
