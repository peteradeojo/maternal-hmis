<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceAuthorization extends Model
{
    use Auditable, Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'authorizable_type',
        'authorizable_id',
        'patient_id',
        'authorization_code',
        'requested_amount',
        'approved_amount',
    ];

    public function authorizable()
    {
        return $this->morphTo();
    }
}
