<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcedureConsent extends Model
{
    protected $fillable = [
        'name', 'procedure', 'relationship',
        'signature_path', 'witnesses', 'user_id', 'admission_id',
        'patient_id',
    ];

    protected $casts = [
        'witnesses' => 'array',
    ];
}
