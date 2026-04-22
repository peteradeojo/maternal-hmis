<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\Auditable;
use App\Traits\CastsStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceProfiles extends Model
{
    use HasFactory, Auditable, CastsStatus;

    protected $fillable = ['hmo_name', 'hmo_company', 'hmo_id_no', 'status'];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }
}
