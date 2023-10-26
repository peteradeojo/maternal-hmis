<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceProfiles extends Model
{
    use HasFactory;

    protected $fillable = ['hmo_name', 'hmo_company', 'hmo_id_no'];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }
}
