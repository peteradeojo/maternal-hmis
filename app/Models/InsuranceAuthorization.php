<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceAuthorization extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'authorizable_type',
        'authorizable_id',
        'patient_id',
        'authorization_code',
    ];

    public function entity()
    {
        return $this->morphTo();
    }
}
